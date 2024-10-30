<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateOrderRequest;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Notifications\OrderStatusChanged;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::with([
            'user',
            'orderDetails',
            'orderStatusHistories',
            'orderStatusHistories.user',
            'orderDetails.variant',
            'orderDetails.variant.weight',
        ]);

        if (!empty($request->input('information'))) {
            $information = $request->input('information');
            $orders = $orders->where(function ($query) use ($information) {
                $query->where('code', 'like', '%' . $information . '%')
                    ->orWhere('name', 'like', '%' . $information . '%')
                    ->orWhere('phone', 'like', '%' . $information . '%');
            });
        }

        if (!empty($request->input('status'))) {
            $orders = $orders->where('status', $request->input('status'));
        }

        $orders = $orders->orderBy('id', 'desc')
            ->paginate(5);

        return view('admin.pages.orders.index', compact('orders'));
    }

    public function updateOrderStatus(UpdateOrderRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $order = Order::find($id);

            if (!$order) {
                return redirect()->route('orders.index')->with('status_failed', 'Đơn hàng không tồn tại!');
            }

            // Lưu trạng thái cũ
            $oldStatus = $order->status;

            // Cập nhật trạng thái mới
            if ($request->new_status == 'completed') {
                $order->update([
                    'payment_status' => 'paid',
                    'status' => $request->new_status,
                ]);
            } else {
                $order->update([
                    'status' => $request->new_status,
                ]);
            }

            // Lưu vào lịch sử thay đổi
            OrderStatusHistory::create([
                'order_id' => $id,
                'old_status' => $oldStatus,
                'new_status' => $request->new_status,
                'changed_by' => auth()->user()->id ?? 0,
                'note' => $request->note,
            ]);

            $order->user->notify(new OrderStatusChanged($order));

            DB::commit();

            return redirect()->route('orders.index')->with('status_succeed', 'Đơn hàng đã được cập nhật thành công!');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error($e->getMessage());

            return back()->with('status_failed', 'Đã xảy ra lỗi khi cập nhật!');
        }
    }


    // Xóa đơn hàng
    public function delete($id)
    {
        try {
            DB::beginTransaction();

            $order = Order::find($id);

            if (!$order) {
                return redirect()->route('orders.index')->with('status_failed', 'Đơn hàng không tồn tại!');
            }

            $order->delete();

            $order->orderDetails()->delete();

            DB::commit();

            return redirect()->route('orders.index')->with('status_succeed', 'Đơn hàng đã được xóa thành công!');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error($e->getMessage());

            return back()->with('status_failed', 'Đã xảy ra lỗi khi xóa!');
        }
    }
}
