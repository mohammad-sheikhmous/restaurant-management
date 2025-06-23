<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
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
            'name' => 'required|array',
            'name.en' => 'required|string|max:30|unique_translation:categories,name,' . $this->category,
            'name.ar' => 'required|string|max:30|unique_translation:categories,name,' . $this->category,
            'image' => request()->is('*/categories') ? 'required' : 'nullable' . '|image|mimes:jpeg,svg,png,jpg,gif,ico',
            'status' => 'in:0,1',
//            'parent' => 'nullable|exists:categories,id'
        ];
    }
}
