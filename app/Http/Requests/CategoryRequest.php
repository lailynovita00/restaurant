<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
{
    public function authorize()
    {
        return true;  
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'requires_sauce' => 'nullable|boolean',
            'sauce_ids' => 'nullable|array',
            'sauce_ids.*' => 'integer|exists:sauces,id',
            'requires_side' => 'nullable|boolean',
            'side_ids' => 'nullable|array',
            'side_ids.*' => 'integer|exists:sides,id',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'The name field is required.',
            'name.string' => 'The name must be a string.',
            'name.max' => 'The name may not be greater than 255 characters.',
            'name_ar.string' => 'The Arabic name must be a string.',
            'name_ar.max' => 'The Arabic name may not be greater than 255 characters.',
            'sauce_ids.array' => 'Sauce list must be valid.',
            'sauce_ids.*.exists' => 'One or more selected sauces are invalid.',
            'side_ids.array' => 'Side list must be valid.',
            'side_ids.*.exists' => 'One or more selected sides are invalid.',
        ];
    }
    protected function prepareForValidation()
    {
        $this->merge([
            'name' => ucwords($this->name),
            'name_ar' => $this->name_ar ? trim($this->name_ar) : null,
            'requires_sauce' => $this->boolean('requires_sauce'),
            'requires_side' => $this->boolean('requires_side'),
        ]);
    }
}
