<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
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
        $data = [
            'name.en' => 'required|string|max:50|unique_translation:products,name,' . $this->product,
            'name.ar' => 'required|string|max:50|unique_translation:products,name,' . $this->product,
            'description.en' => 'required|string|max:120',
            'description.ar' => 'required|string|max:120',
            'status' => 'required|in:0,1',
            'is_simple' => 'required|in:0,1',
            'price' => 'nullable|required_if:is_simple,1|decimal:0,2',
            'is_recommended' => 'required|in:0,1',
            'image' => request()->is('*/products') ? 'required' : 'nullable' . '|image|mimes:jpeg,svg,png,jpg,gif,ico',
            'category_id' => 'required|exists:categories,id',
            'tags_ids' => 'required|array|min:1',
            'tags_ids.*' => 'required|exists:tags,id',
        ];
        if ($this->is_simple == 0) {
            $data['basic_attribute_id'] = [
                'required',
                'integer',
                Rule::exists('attributes', 'id')->where('type', 'basic')
            ];
            $data['basic_options_ids'] = 'required|array';
            $data['basic_options_ids.*'] = [
                'required',
                'integer',
                Rule::exists('attribute_options', 'id')
                    ->where('attribute_id', $this->basic_attribute_id)
            ];
            $data['basic_opts_prices'] = 'required|array|size:' . count($this->basic_options_ids);
            $data['basic_opts_prices.*'] = 'required|decimal:0,2';
            $data['default_basic_opt'] = ['required', 'integer', Rule::in($this->basic_options_ids)];
        }

        $data['additional_attributes_ids.*'] = [
            'required',
            'integer',
            Rule::exists('attributes', 'id')->where('type', 'additional')
        ];
        $data['additional_options_ids'] = 'nullable|array|size:' . count($this->additional_attributes_ids ?? []);
        $data['additional_options_ids.*.*'] = [
            'required',
            'integer',
            Rule::exists('attribute_options', 'id')
                ->whereIn('attribute_id', $this->additional_attributes_ids)
        ];
        $data['extra_prices'] = 'nullable|array|size:' . count($this->additional_attributes_ids ?? []);
        $data['extra_prices.*'] = ['required', 'array', function ($attribute, $value, $fail) {
            $index = explode('.', $attribute)[1];
            $expectedCount = count($this->additional_options_ids[$index] ?? []);
            if (count($value) !== $expectedCount) {
                $fail("The {$attribute} field must contain {$expectedCount} items.");
            }
        }];
        $data['extra_prices.*.*'] = 'required|decimal:0,2';
//        $data['is_default'] = 'nullable|array|size:' . $this->additional_attributes_ids->count();
        $data['default_add_opts.*'] = ['required', 'integer', Rule::in(Arr::flatten($this->additional_options_ids))];

        return $data;
    }
}
