<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'code' => 'required',
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'total_price' => 'required|numeric',
            'status' => 'nullable|in:pending,shipping,delivering,completed,cancelled',
            'payment_method' => 'nullable|in:cod,vnpay',
            'payment_status' => 'nullable|in:unpaid,paid',
            'products' => 'required|array',
            'products.*.product_id' => 'required|integer|exists:products,id',
            'products.*.variant_id' => 'nullable|integer|exists:variants,id',
            'products.*.price' => 'required|numeric',
            'products.*.quantity' => 'required|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'Mã đơn hàng là bắt buộc.',
            'name.required' => 'Tên người nhận là bắt buộc.',
            'name.string' => 'Tên người nhận phải là chuỗi ký tự.',
            'name.max' => 'Tên người nhận không được vượt quá :max ký tự.',

            'address.required' => 'Địa chỉ giao hàng là bắt buộc.',
            'address.string' => 'Địa chỉ giao hàng phải là chuỗi ký tự.',
            'address.max' => 'Địa chỉ giao hàng không được vượt quá :max ký tự.',

            'phone.required' => 'Số điện thoại là bắt buộc.',
            'phone.string' => 'Số điện thoại phải là chuỗi ký tự.',
            'phone.max' => 'Số điện thoại không được vượt quá :max ký tự.',

            'total_price.required' => 'Tổng giá trị đơn hàng là bắt buộc.',
            'total_price.numeric' => 'Tổng giá trị đơn hàng phải là số.',

            'status.in' => 'Trạng thái đơn hàng không hợp lệ.',

            'payment_method.in' => 'Hình thức thanh toán không hợp lệ.',

            'payment_status.in' => 'Trạng thái thanh toán không hợp lệ.',

            'products.required' => 'Danh sách sản phẩm là bắt buộc.',
            'products.array' => 'Danh sách sản phẩm phải là mảng.',

            'products.*.product_id.required' => 'ID sản phẩm là bắt buộc.',
            'products.*.product_id.integer' => 'ID sản phẩm phải là số nguyên.',
            'products.*.product_id.exists' => 'Sản phẩm không tồn tại.',

            'products.*.variant_id.integer' => 'ID biến thể phải là số nguyên.',
            'products.*.variant_id.exists' => 'Biến thể không tồn tại.',

            'products.*.price.required' => 'Giá sản phẩm là bắt buộc.',
            'products.*.price.numeric' => 'Giá sản phẩm phải là số.',

            'products.*.quantity.required' => 'Số lượng sản phẩm là bắt buộc.',
            'products.*.quantity.integer' => 'Số lượng sản phẩm phải là số nguyên.',
            'products.*.quantity.min' => 'Số lượng sản phẩm phải ít nhất là 1.',
        ];
    }

}
