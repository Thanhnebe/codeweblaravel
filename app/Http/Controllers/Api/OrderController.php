<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderRequest;
use App\Mail\OrderConfirmation;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderStatusHistory;
use App\Models\Variant;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    // Danh sách đơn hàng theo người dùng
    public function getAllOrderByUser(Request $request, $userId)
    {
        try {
            $orders = Order::with([
                'user',
                'orderDetails',
                'orderDetails.variant',
                'orderDetails.variant.product',
                'orderDetails.variant.weight',
            ]);

            if (!empty($request->code)) {
                $orders = $orders->where('code', $request->code);
            }

            $orders = $orders->where('user_id', $userId)
                ->orderBy('id', 'desc')
                ->get();

            if ($orders->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Không có đơn hàng nào!'
                ], Response::HTTP_NOT_FOUND);
            }

            return response()->json([
                'status' => true,
                'message' => 'Danh sách đơn hàng đã được lấy thành công.',
                'data' => $orders
            ], Response::HTTP_OK);
        } catch (QueryException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Đã xảy ra lỗi với cơ sở dữ liệu.',
                'errors' => [$e->getMessage()],
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi models không tạo.',
                'errors' => [$e->getMessage()],
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Exception $e) {
            // Lỗi hệ thống
            return response()->json([
                'status' => false,
                'message' => 'Đã xảy ra lỗi khi truy xuất dữ liệu',
                'errors' => [$e->getMessage()],
                'code' => $e->getCode()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // Chi tiết đơn hàng
    public function getOrderDetails($orderId)
    {
        try {
            $order = Order::with([
                'user',
                'orderDetails',
                'orderDetails.variant',
                'orderDetails.variant.product',
                'orderDetails.variant.weight',
            ])
                ->where('id', $orderId)
                ->first();

            if (!$order) {
                return response()->json([
                    'status' => false,
                    'message' => 'Không tìm thấy đơn hàng!'
                ], Response::HTTP_NOT_FOUND);
            }

            return response()->json([
                'status' => true,
                'message' => 'Danh sách chi tiết đơn hàng',
                'data' => $order
            ], Response::HTTP_OK);
        } catch (QueryException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Đã xảy ra lỗi với cơ sở dữ liệu.',
                'errors' => [$e->getMessage()],
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi models không tạo.',
                'errors' => [$e->getMessage()],
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Exception $e) {
            // Lỗi hệ thống
            return response()->json([
                'status' => false,
                'message' => 'Đã xảy ra lỗi khi truy xuất dữ liệu',
                'errors' => [$e->getMessage()],
                'code' => $e->getCode()
            ]);
        }
    }

    // Hủy đơn hàng
    public function cancelOrder($orderId)
    {
        DB::beginTransaction();
        try {
            // Lấy đơn hàng theo ID
            $order = Order::find($orderId);

            if (!$order) {
                return response()->json([
                    'status' => false,
                    'message' => 'Không tìm thấy đơn hàng!'
                ], Response::HTTP_NOT_FOUND);
            }

            // Kiểm tra trạng thái đơn hàng
            if ($order->status !== 'pending') {
                return response()->json([
                    'status' => false,
                    'message' => 'Chỉ có thể hủy đơn hàng khi đang ở trạng thái "Chờ xác nhận".'
                ], Response::HTTP_BAD_REQUEST);
            }

            // Lấy danh sách các sản phẩm trong đơn hàng (OrderDetail)
            $orderDetails = OrderDetail::where('order_id', $order->id)->get();

            // Duyệt qua từng sản phẩm trong chi tiết đơn hàng
            foreach ($orderDetails as $detail) {
                // Lấy biến thể sản phẩm từ bảng variants
                $variant = Variant::find($detail->variant_id);

                if ($variant) {
                    // Cập nhật lại số lượng cho biến thể (cộng lại số lượng đã mua)
                    $variant->quantity += $detail->quantity;
                    $variant->save();
                }
            }

            // Cập nhật trạng thái đơn hàng thành "Đã hủy"
            $order->update([
                'status' => 'cancelled'
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Đơn hàng đã hủy thành công',
                'order' => $order
            ], Response::HTTP_OK);
        } catch (QueryException $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Đã xảy ra lỗi với cơ sở dữ liệu.',
                'errors' => [$e->getMessage()],
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Lỗi models không tạo.',
                'errors' => [$e->getMessage()],
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Đã xảy ra lỗi khi truy xuất dữ liệu',
                'errors' => [$e->getMessage()],
                'code' => $e->getCode()
            ]);
        }
    }

    // Đánh dấu đơn hàng là hoàn thành
    public function markAsCompleted($orderId)
    {
        DB::beginTransaction();
        try {
            // Lấy đơn hàng theo ID
            $order = Order::find($orderId);

            if (!$order) {
                return response()->json([
                    'status' => false,
                    'message' => 'Không tìm thấy đơn hàng!'
                ], Response::HTTP_NOT_FOUND);
            }

            // Kiểm tra trạng thái đơn hàng
            if ($order->status !== 'delivering') {
                return response()->json([
                    'status' => false,
                    'message' => 'Chỉ có thể hoàn thành đơn hàng khi đang ở trạng thái "Đang giao hàng".'
                ], Response::HTTP_BAD_REQUEST);
            }

            // Cập nhật trạng thái đơn hàng thành "Hoàn thành" và "Đã thanh toán"
            $order->update([
                'status' => 'completed',
                'payment_status' => 'paid'
            ]);

            // Lưu lại lịch sử trạng thái đơn hàng
            OrderStatusHistory::create([
                'order_id' => $order->id,
                'old_status' => 'delivering',
                'new_status' => 'completed',
                'changed_by' => auth()->user()->id ?? 0, // Cập nhật nếu có người dùng đang đăng nhập
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Đơn hàng đã được hoàn thành thành công',
                'order' => $order
            ], Response::HTTP_OK);
        } catch (QueryException $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Đã xảy ra lỗi với cơ sở dữ liệu.',
                'errors' => [$e->getMessage()],
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Lỗi models không tạo.',
                'errors' => [$e->getMessage()],
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Đã xảy ra lỗi khi truy xuất dữ liệu',
                'errors' => [$e->getMessage()],
                'code' => $e->getCode()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function generateRandomOrderCode($length = 8)
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }

        return $randomString;
    }

    public function storeOrder(Request $request)
    {
        DB::beginTransaction();
        try {
            // Kiểm tra phương thức thanh toán
            if ($request->payment_method == 'vnpay') {
                $orderCode = $this->generateRandomOrderCode(8);
                $vnpayUrl = $this->generateVnpayUrl($orderCode, $request->total_price);

                // Tạo đơn hàng tạm thời với trạng thái chờ thanh toán
                $order = Order::create([
                    'user_id' => $request->user_id ?? null,
                    'code' => $orderCode,
                    'name' => $request->name,
                    'address' => $request->address,
                    'phone' => $request->phone,
                    'shipping_fee' => $request->shipping_fee ?? 0,
                    'total_price' => $request->total_price,
                    'status' => 'pending',
                    'payment_method' => 'vnpay',
                    'payment_status' => 'unpaid',
                ]);

                // Lưu chi tiết sản phẩm trong đơn hàng
                foreach ($request->products as $product) {
                    // Tìm variant với khóa để kiểm tra tồn kho
                    $variant = Variant::lockForUpdate()->find($product['variant_id']);
                    if ($variant && $variant->quantity >= $product['quantity']) {
                        // Trừ số lượng sản phẩm và lưu chi tiết đơn hàng
                        $variant->update([
                            'quantity' => $variant->quantity - $product['quantity']
                        ]);

                        OrderDetail::create([
                            'order_id' => $order->id,
                            'variant_id' => $product['variant_id'] ?? null,
                            'price' => $product['price'],
                            'quantity' => $product['quantity'],
                            'total' => $product['price'] * $product['quantity'],
                        ]);
                    } else {
                        // Trả về lỗi nếu hết hàng
                        DB::rollBack();
                        return response()->json([
                            'status' => false,
                            'message' => 'Đã vượt quá số lượng sản phẩm.',
                        ]);
                    }
                }

                DB::commit();
                return response()->json([
                    'status' => true,
                    'message' => 'Chuyển hướng đến VNPay.',
                    'vnpay_url' => $vnpayUrl['data'],
                    'payment_method' => 'vnpay'
                ]);
            } else {
                // Xử lý thanh toán khi nhận hàng (COD)
                $order = Order::create([
                    'user_id' => $request->user_id ?? null,
                    'code' => $this->generateRandomOrderCode(8),
                    'name' => $request->name,
                    'address' => $request->address,
                    'phone' => $request->phone,
                    'shipping_fee' => $request->shipping_fee ?? 0,
                    'total_price' => $request->total_price,
                    'status' => $request->status ?? 'pending',
                    'payment_method' => $request->payment_method ?? 'cod',
                    'payment_status' => 'unpaid',
                ]);

                // Lưu chi tiết sản phẩm trong đơn hàng
                foreach ($request->products as $product) {
                    $variant = Variant::lockForUpdate()->find($product['variant_id']);
                    if ($variant && $variant->quantity >= $product['quantity']) {
                        $variant->update([
                            'quantity' => $variant->quantity - $product['quantity']
                        ]);

                        OrderDetail::create([
                            'order_id' => $order->id,
                            'variant_id' => $product['variant_id'] ?? null,
                            'price' => $product['price'],
                            'quantity' => $product['quantity'],
                            'total' => $product['price'] * $product['quantity'],
                        ]);
                    } else {
                        DB::rollBack();
                        return response()->json([
                            'status' => false,
                            'message' => 'Đặt hàng thất bại do số lượng tồn kho đã hết.',
                        ]);
                    }
                }

                // Lưu lại lịch sử trạng thái đơn hàng ban đầu
                OrderStatusHistory::create([
                    'order_id' => $order->id,
                    'old_status' => $order->status,
                    'new_status' => $order->status,
                    'changed_by' => auth()->user()->id ?? 0,
                ]);
                Mail::to($request->email)->send(new OrderConfirmation($order));
                // Xóa sản phẩm trong giỏ hàng sau khi đặt hàng thành công
                foreach ($request->products as $product) {
                    Cart::where('user_id', $request->user_id)
                        ->where('product_id', $product['product_id'])
                        ->delete();
                }

                DB::commit();
                return response()->json([
                    'status' => true,
                    'message' => 'Đơn hàng đã được tạo thành công với phương thức thanh toán COD.',
                    'order_id' => $order->id,
                    'payment_method' => 'cod'
                ], 201);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Đã xảy ra lỗi trong quá trình tạo đơn hàng',
                'errors' => [$e->getMessage()],
                'code' => $e->getCode(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }



    // Thanh toán qua VNPay
    public function generateVnpayUrl($orderCode, $totalPrice) // Thêm tham số $request
    {
        $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
        $vnp_Returnurl = route('orders.vnpayReturn');
        $vnp_TmnCode = "G2QKJU4Y";
        $vnp_HashSecret = "PMDGOWFWONAWTIYOLFWSEKPJGHNIQBJE";

        $vnp_TxnRef = $orderCode;
        $vnp_OrderInfo = "Thanh toán đơn hàng";
        $vnp_OrderType = "billpayment";
        $vnp_Amount = $totalPrice * 100;
        $vnp_Locale = "vn";
        $vnp_BankCode = "NCB";
        $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];

        // Thêm các dữ liệu thanh toán
        $inputData = [
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef
        ];

        if (isset($vnp_BankCode) && $vnp_BankCode != "") {
            $inputData['vnp_BankCode'] = $vnp_BankCode;
        }
        if (isset($vnp_Bill_State) && $vnp_Bill_State != "") {
            $inputData['vnp_Bill_State'] = $vnp_Bill_State;
        }

        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $vnp_Url . "?" . $query;
        if (isset($vnp_HashSecret)) {
            $vnpSecureHash =   hash_hmac('sha512', $hashdata, $vnp_HashSecret); //  
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }

        return [
            'code' => '00',
            'message' => 'success',
            'data' => $vnp_Url
        ];
    }

    // Thanh toán qua VNPay
    public function vnpayReturn(Request $request)
    {
        $vnp_SecureHash = $request->get('vnp_SecureHash');
        $vnp_TxnRef = $request->get('vnp_TxnRef');
        $vnp_Amount = $request->get('vnp_Amount') / 100;
        $vnp_ResponseCode = $request->get('vnp_ResponseCode');

        // Kiểm tra mã phản hồi
        if ($vnp_ResponseCode == '00') {
            // Thanh toán thành công
            DB::beginTransaction();
            try {
                // Tìm đơn hàng dựa trên mã giao dịch
                $order = Order::where('code', $vnp_TxnRef)->first();

                if ($order) {
                    $order->update([
                        'payment_status' => 'paid',
                        'total_price' => $vnp_Amount,
                    ]);

                    OrderStatusHistory::create([
                        'order_id' => $order->id,
                        'old_status' => 'pending',
                        'new_status' => 'confirmed',
                        'changed_by' => auth()->user()->id ?? 0,
                    ]);

                    // Xóa sản phẩm trong giỏ hàng của người dùng
                    foreach ($order->orderDetails as $orderDetail) {
                        Cart::where('user_id', $order->user_id)
                            ->where('product_id', $orderDetail->variant_id) // Giả sử variant_id là product_id trong giỏ hàng
                            ->delete();
                    }

                    DB::commit();

                    return redirect()->to('http://localhost:5173/confirm');
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'Không tìm thấy đơn hàng.',
                    ]);
                }
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => 'Đã xảy ra lỗi khi xử lý đơn hàng.',
                    'errors' => [$e->getMessage()],
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        } else {
            // Thanh toán đã bị hủy hoặc thất bại
            DB::beginTransaction();
            try {
                $order = Order::where('code', $vnp_TxnRef)->first();

                if ($order) {
                    $order->update([
                        'payment_status' => 'unpaid',
                        'status' => 'cancelled', // Trạng thái bạn muốn đặt
                    ]);

                    OrderStatusHistory::create([
                        'order_id' => $order->id,
                        'old_status' => 'pending',
                        'new_status' => 'canceled',
                        'changed_by' => auth()->user()->id ?? 0,
                    ]);

                    DB::commit();

                    return redirect()->to('http://localhost:5173/confirm-cancel');
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'Không tìm thấy đơn hàng.',
                    ]);
                }
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => 'Đã xảy ra lỗi khi cập nhật trạng thái đơn hàng.',
                    'errors' => [$e->getMessage()],
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
    }
}
