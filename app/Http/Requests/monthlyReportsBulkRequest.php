<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class monthlyReportsBulkRequest extends Request
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
            '*.speciality_id' => 'required',
            'judge_id' => 'required',
            '*.rotated' => 'required|numeric',
            '*.pretencesArrival' => 'numeric',
            '*.arriving' => 'numeric',
            '*.arrivalDirectComplaint' => 'numeric',
            '*.eliminatedArrival' => 'numeric',
            '*.totalSeparated' => 'required|numeric',
            '*.casesOnSchedule' => 'numeric',
            '*.totalCases' => 'required|numeric',
            '*.remainedCases' => 'required|numeric',
            'year' => 'required',
            'month' => 'required',
        ];
    }
    
    public function messages()
    {
        return [
            'judge_court_id.required' => 'يجب إختيار محكمة',
            'speciality_id.*.required'  => 'يجب تحديد الإختصاص',
            'judge_id.required'  => 'يجب تحديد القاضي',
            'rotated.*.required' => 'يجب تحديد عدد الدعاوى المدوّرة',
            'rotated.*.numeric' => 'المدوّر يجب أن يكون بالأرقام',
            'pretencesArrival.*.numeric' => 'وارد إدعاء نيابة يجب أن يكون بالأرقام',
            'arriving.*.numeric' => 'الوارد يجب أن يكون بالأرقام',
            'arrivalDirectComplaint.*.numeric' => 'وارد شكوى مباشرة يجب أن يكون بالأرقام',
            'eliminatedArrival.*.numeric' => 'الوارد من الشمطوب يجب أن يكون بالأرقام',
            'casesOnSchedule.*.numeric'  => 'عدد الدعاوى على الجدول يجب أن يكون بالأرقام',
            'totalSeparated.*.required'  => 'يجب تحديد مجموع المفصول ',
            'totalSeparated.*.numeric'  => 'مجموع المفصول يجب أن يكون بالأرقام',
            'totalCases.*.required'  => 'يجب تحديد المجموع العام',
            'totalCases.*.numeric'  => 'المجموع العام يجب أن يكون بالأرقام',
            'remainedCases.*.required'  => 'يجب تحديد الباقي',
            'remainedCases.*.numeric'  => 'الباقي يجب أن يكون بالأرقام',
            'year.required'  => 'يجب إختيار السنة',
            'month.required'  => 'يجب إختيار الشهر',
        ];
    }
}
