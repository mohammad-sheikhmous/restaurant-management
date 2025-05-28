<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttributeRequest extends FormRequest
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
            'name.en' => 'required|string|max:40|unique_translation:attributes,name,' . $this->attribute,
            'name.ar' => 'required|string|max:40|unique_translation:attributes,name,' . $this->attribute,
            'type' => 'required|in:basic,additional',
            'options.*.en' => 'required|string|max:40|unique_translation:attribute_options,name,' . $this->attribute . ',attribute_id',
            'options.*.ar' => 'required|string|max:40|unique_translation:attribute_options,name,' . $this->attribute . ',attribute_id',
        ];
    }
}
