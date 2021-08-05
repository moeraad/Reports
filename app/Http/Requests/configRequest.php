<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class configRequest extends Request
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
            'province_id' => 'required',
            'district_id' => 'required',
            'court_degree_id' => 'required',
            'role_id' => 'required',
            'count' => 'required|numeric',
        ];
    }
}
