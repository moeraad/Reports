<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class judgmentBulkRequest extends Request
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
            'judge_court_id' => 'required',
            'report_date' => 'required',
            'judge_id' => 'required',
            'rule_number.*' => 'numeric',
            'sessions_count.*' => 'numeric',
            'judgement_date.*' => 'required',
            'arrival_date.*' => 'required|date',
            'judgement_date.*' => 'required|date',
            'speciality_id.*' => 'required_without:judgment_type_id',
            'judgment_type_id.*' => 'required_without:speciality_id',
        ];
    }
    
    public function messages()
    {
        return [
            'judge_court_id.required' => 'يجب إختيار المحكمة',
            'report_date.required' => 'يجب إختيار تاريخ الجدول الشهري',
            'judge_id.required' => 'يجب إختيار القاضي المسؤول عن إصدار الحكم',
            'rule_number.*.numeric' => 'رقم الحكم يجب أن لا يتضمن أحرف أو رموز',
            'sessions_count.*.numeric' => 'عدد الجلسات يجب أن لا يتضمن أحرف أو رموز',
            'arrival_date.*.required' => 'يجب تحديد تاريخ الورود',
            'judgement_date.*.required' => 'يجب إختيار تاريخ صدور الحكم',
            'arrival_date.*.date' => 'تاريخ الورود غير صحيح',
            'judgement_date.*.date' => 'تاريخ صدور الحكم غير صحيح',
            'judgment_type_id.*.required_without' => "يجب إختيار نوع الحكم في حال لم يحدّد الإختصاص",
            'speciality_id.*.required_without' => "يجب إختيار الإختصاص في حال لم يحدّد نوع الحكم"
        ];
    }
}
