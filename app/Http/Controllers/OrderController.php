<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Mail\OrderConfirmation;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    public function storeOrder(Request $request)
    {

        // Validate the incoming request data
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'total_price' => 'required|numeric',
            'name' => 'required|string',
            'address' => 'required|string',
            'phone' => 'required|string',
            'products' => 'required|array',
            'payment_method' => 'required|string',
            'code' => 'required|string',
            'email' => 'required|email',
        ]);

        // Create a new order
        $order = Order::create([
            'user_id' => $validatedData['user_id'],
            'total_price' => $validatedData['total_price'],
            'status' => 'pending', // Initial status
            'name' => $validatedData['name'],
            'address' => $validatedData['address'],
            'phone' => $validatedData['phone'],
            'code' => $validatedData['code'],
            'payment_method' => $validatedData['payment_method'] ?? "cod",
            'shipping_fee' => $request['shipping_fee'] ?? 0,
        ]);

        // Loop through products and create order details
        foreach ($validatedData['products'] as $product) {
            $total = $product['price'] * $product['quantity'];

            OrderDetail::create([
                'order_id' => $order->id,
                'product_id' => $product['product_id'],
                'variant_id' => $product['variant_id'],
                'price' => $product['price'],
                'quantity' => $product['quantity'],
                'total' => $total,
            ]);
        }

        // Send confirmation email
        Mail::to($validatedData['email'])->send(new OrderConfirmation($order));

        // Prepare response
        return response()->json([
            'status' => true,
            'message' => 'Order placed successfully!',
            'payment_method' => $validatedData['payment_method'],
            // Add vnpay_url here if needed
        ]);
    }
}
