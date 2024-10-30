<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CartRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Có thể điều chỉnh tùy theo yêu cầu xác thực của bạn
    }

    public function rules()
    {
        return [
            'user_id' => 'required',
            'product_id' => 'required',
            'variant_id' => 'nullable',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric',
        ];
    }

    public function messages()
    {
        return [
            'user_id.required' => 'User ID is required.',
            'product_id.required' => 'Product ID is required.',
            'quantity.required' => 'Quantity is required.',
            'price.required' => 'Price is required.',
        ];
    }
}
