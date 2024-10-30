<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WeightRequest extends FormRequest
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
        $id = $this->route('id');
        return [
            'weight' => 'required|numeric|unique:weights,weight,' . $id,
            'unit' => 'required'
        ];
    }

    public function messages(): array
    {
        return [
            'weight.required' => 'Vui lòng nhập trọng lượng',
            'weight.numeric' => 'Trọng lượng phải là số',
            'weight.unique' => 'Trọng lượng này đã tồn tại!',
            'unit.required' => 'Vui lòng chọn đơn vị tính'
        ];
    }
}
