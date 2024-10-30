<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CartRequest;
use App\Models\Cart;
use App\Models\Variant;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function cartByUserId($userId)
    {
        try {
            $cartItems = Cart::with([
                'product',
                'variant',
                'variant.product',
                'variant.weight',
                'user'
            ])
                ->where('user_id', $userId)
                ->get();

            if ($cartItems->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Giỏ hàng trống.',
                ], Response::HTTP_NOT_FOUND);
            }

            return response()->json([
                'status' => true,
                'message' => 'Danh sách giỏ hàng đã được lấy thành công.',
                'cart_items' => $cartItems,
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
            return response()->json([
                'status' => false,
                'message' => 'Đã xảy ra lỗi, vui lòng thử lại.',
                'errors' => [$e->getMessage()],
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function addToCart(CartRequest $request)
    {
        DB::beginTransaction();
        try {
            // Kiểm tra tồn kho của biến thể sản phẩm
            $variant = Variant::find($request->variant_id);
            if (!$variant) {
                return response()->json([
                    'status' => false,
                    'message' => 'Biến thể sản phẩm không tồn tại.',
                ], Response::HTTP_BAD_REQUEST);
            }

            // Kiểm tra nếu sản phẩm đã có trong giỏ hàng
            $cartItem = Cart::where('user_id', $request->user_id)
                ->where('product_id', $request->product_id)
                ->where('variant_id', $request->variant_id)
                ->first();

            if ($cartItem) {
                // Nếu sản phẩm đã có trong giỏ hàng
                $newQuantity = $cartItem->quantity + $request->quantity;

                // Kiểm tra nếu số lượng mới vượt quá số lượng tồn kho
                if ($newQuantity > $variant->quantity) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Số lượng yêu cầu vượt quá số lượng tồn kho của sản phẩm.',
                    ], Response::HTTP_BAD_REQUEST);
                }

                // Cập nhật số lượng trong giỏ hàng
                $cartItem->quantity = $newQuantity;
                $cartItem->save();
            } else {
                // Nếu sản phẩm chưa có trong giỏ hàng
                if ($request->quantity > $variant->quantity) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Số lượng yêu cầu vượt quá số lượng tồn kho của sản phẩm.',
                    ], Response::HTTP_BAD_REQUEST);
                }

                // Thêm sản phẩm mới vào giỏ hàng
                $cartItem = Cart::create([
                    'user_id' => $request->user_id,
                    'product_id' => $request->product_id,
                    'variant_id' => $request->variant_id,
                    'quantity' => $request->quantity,
                    'price' => $request->price,
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Sản phẩm đã được thêm vào giỏ hàng thành công.',
                'cart_item' => $cartItem,
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
                'message' => 'Lỗi không thấy model.',
                'errors' => [$e->getMessage()],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Đã xảy ra lỗi, vui lòng thử lại.',
                'errors' => [$e->getMessage()],
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function updateQuantity(Request $request)
    {
        DB::beginTransaction();
        try {
            $cartItem = Cart::find($request->cart_id);

            if (!$cartItem) {
                return response()->json([
                    'status' => false,
                    'message' => 'Sản phẩm không tồn tại.',
                ], Response::HTTP_NOT_FOUND);
            }

            // Lấy thông tin biến thể để kiểm tra tồn kho
            $variant = Variant::find($cartItem->variant_id);
            if (!$variant) {
                return response()->json([
                    'status' => false,
                    'message' => 'Biến thể sản phẩm không tồn tại.',
                ], Response::HTTP_BAD_REQUEST);
            }

            // Kiểm tra nếu số lượng yêu cầu vượt quá số lượng tồn kho
            if ($request->quantity > $variant->quantity) {
                return response()->json([
                    'status' => false,
                    'message' => 'Số lượng yêu cầu vượt quá số lượng tồn kho của sản phẩm.',
                ], Response::HTTP_BAD_REQUEST);
            }

            // Cập nhật số lượng nếu hợp lệ
            $cartItem->update([
                'quantity' => $request->quantity,
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Số lượng sản phẩm đã được thay đổi',
                'cart_item' => $cartItem,
            ], Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Sản phẩm không tồn tại.',
                'errors' => [$e->getMessage()],
            ], Response::HTTP_NOT_FOUND);
        } catch (QueryException $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Đã xảy ra lỗi với cơ sở dữ liệu.',
                'errors' => [$e->getMessage()],
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Đã xảy ra lỗi, vui lòng thử lại.',
                'errors' => [$e->getMessage()],
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deleteCart($id)
    {
        DB::beginTransaction();
        try {
            $cartItem = Cart::find($id);

            if (!$cartItem) {
                return response()->json([
                    'status' => false,
                    'message' => 'Sản phẩm không tồn tại.',
                ], Response::HTTP_NOT_FOUND);
            }

            $cartItem->delete();

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Sản phẩm đã được xóa.',
            ], Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Sản phẩm không tồn tại.',
                'errors' => [$e->getMessage()],
            ], Response::HTTP_NOT_FOUND);
        } catch (QueryException $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Đã xảy ra lỗi với cơ sở dữ liệu.',
                'errors' => [$e->getMessage()],
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Đã xảy ra lỗi!',
                'errors' => [$e->getMessage()],
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
