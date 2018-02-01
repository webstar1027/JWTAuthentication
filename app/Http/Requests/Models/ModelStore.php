<?php

namespace App\Http\Requests\Models;

use App\Http\Requests\BaseRequest;

class ModelStore extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|unique:equipment_models,name',
            'category_id' => 'exists:equipment_categories,id',
            'description' => 'nullable|string',
        ];
    }
}
