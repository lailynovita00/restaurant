<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MenuRequest extends FormRequest
{
    public function authorize()
    {
        return true;  
    }

    public function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'name_ar' => 'nullable|string|max:255',
            'description_ar' => 'nullable|string',
            'video_url' => ['nullable', 'url', 'max:2000'],
        ];

        if ($this->isMethod('post')) {
            $rules['image'] = 'required|image|max:2048';
            $rules['video_url'][] = Rule::unique('menus', 'video_url');
        }

        if ($this->isMethod('put') || $this->isMethod('patch')) {
            $rules['image'] = 'nullable|image|max:2048';
            $rules['video_url'][] = Rule::unique('menus', 'video_url')->ignore($this->route('id'));
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'video_url.unique' => 'Video URL sudah dipakai oleh menu lain. Gunakan link video yang berbeda untuk setiap menu.',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'name' => ucwords($this->name),
            'name_ar' => $this->name_ar ? ucwords($this->name_ar) : null,
        ]);
    }
}
