<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class CourtRequest extends Request
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
            'title' => 'required|min:10',
            //'court_type_id' => 'required',
            'province_id' => 'required',
            'court_degree_id' => 'required',
            'court_name_id' => 'required',
            'room' => 'numeric',
        ];
    }
}
