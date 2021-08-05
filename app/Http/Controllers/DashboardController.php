<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\judge;
use App\judgeCourt;
use App\Judgement;
use App\monthlyReport;
use App\Speciality;
use App\userProfile;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Yajra\Datatables\Datatables;

class DashboardController extends Controller
{

    function index()
    {
        /* $this->ReadClerks();
          exit; */
        $this->RegisterTable();
        $this->updateJudgesDegrees();
        $this->RetireJudges();
        $this->manageSession();

        $line_chart = DashboardController::getSpecialitiesStats();

        $records_year = DashboardController::recordsCount("year");
        $records_today = DashboardController::recordsCount("today");

        $records_today_all = DashboardController::recordsCount("today", true);
        $records_year_all = DashboardController::recordsCount("year", true);

        $judgments_year = DashboardController::recordsCount("year", false, "judgments");
        $judgments_today = DashboardController::recordsCount("today", false, "judgments");

        $judgments_today_all = DashboardController::recordsCount("today", true, "judgments");
        $judgments_year_all = DashboardController::recordsCount("year", true, "judgments");
        $page_title = "بيانات";

        $months = ['Sep', 'Oct', 'Nov', 'Dec', 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug'];
        return view("dashboard", compact("page_title", "line_chart", "records_today", "records_year", "records_today_all", "records_year_all", "months", "judgments_today", "judgments_year", "judgments_today_all", "judgments_year_all"));
    }

    public function manageSession()
    {
        if (empty(Session::get('provinces')))
        {
            $user_profile = userProfile::where("user_id", Auth::user()->id)->first();
            Session::put('provinces', $user_profile->provinces);
        }

        if (empty(Session::get('current_year')))
        {
            $user_profile = userProfile::where("user_id", Auth::user()->id)->first();
            Session::put('current_year', $user_profile->current_year);
        }
    }

    public function updateJudgesDegrees()
    {
        $valid_date = Carbon::create(1970, 1, 1);
        $judges = judge::where('date_promotion', '<', Carbon::now())->where('retired', 0)->where('date_promotion', '>', $valid_date)->get();

        while ($judges->count() != 0)
        {
            foreach ($judges as $judge)
            {
                if ($judge->date_promotion < Carbon::now())
                {
                    $judge->degree += 1;
                    $judge->date_promotion = Carbon::createFromFormat("Y-m-d", $judge->date_promotion)->addYear(2);
                    $judge->save();
                }
            }

            $judges = judge::where('date_promotion', '<', Carbon::now())->where('retired', 0)->where('date_promotion', '>', $valid_date)->get();
        }
    }

    public function RetireJudges()
    {
        $judges = judge::where('birth_date', '<', Carbon::now()->subYear('68')->toDateString())->where('birth_date', '<>', '0000-00-00')->where('retired', 0)->get();

        foreach ($judges as $judge)
        {
            $judge->retired = 1;
            $judge->save();
        }
    }

    public function getSpecialitiesStats()
    {
        $stats = array();
        $specialities = Speciality::where('show_chart', 1)->lists("title", "id")->toArray();
        $data_range = GetRange();

        $reports = DB::select(DB::raw('SELECT `speciality_id`, `month`, SUM(`totalSeparated`) as cnt '
                                . 'FROM `monthly_reports` '
                                . 'where ((month BETWEEN "9" and "12" and year="' . date("Y", strtotime($data_range["from"])) . '") or '
                                . '(month BETWEEN "1" and "8" and year="' . date("Y", strtotime($data_range["to"])) . '") ) '
                                . 'and `speciality_id` in (' . implode(",", array_keys($specialities)) . ') '
                                . 'GROUP BY `speciality_id`,`monthly_reports`.`month` '
                                . 'ORDER BY `monthly_reports`.`month` ASC'));

        foreach ($reports as $report)
        {
            $index = (( $report->month + 4 ) % 12) - 1;
            $speciality = $report->speciality_id;
            if (!empty($speciality))
            {
                $stats[$speciality]["name"] = $specialities[$speciality];
                $stats[$speciality]["data"][$index] = $report->cnt;
            }
        }

        return $stats;
    }

    public function recordsCount($period = "year", $all_users = false, $type = "reports")
    {
        if ($period == "today")
        {
            $begin = date("Y-m-d 00:00:00");
            $end = date("Y-m-d 23:59:59");
        } else
        {
            $begin = date("Y-09-01 00:00:00", strtotime("-1 year"));
            $end = date("Y-12-t 23:59:59"); //it returns the num of days in month
        }

        $user_id = Auth::user()->id;

        if ($type == "reports")
        {
            if ($all_users == false)
            {
                $reports_count = monthlyReport::where(function($query) use ($user_id)
                        {
                            $query->where('created_by', $user_id)->orWhere('modified_by', $user_id);
                        })->where(function($query) use ($begin, $end)
                        {
                            $query->where('created_at', ">", $begin)->where('created_at', "<", $end);
                        })->where(function($query) use ($begin, $end)
                        {
                            $query->where('updated_at', ">", $begin)->where('updated_at', "<", $end);
                        })->count();
            } else
            {
                $reports_count = monthlyReport::where(function($query) use ($begin, $end)
                        {
                            $query->where('created_at', ">", $begin)->where('created_at', "<", $end);
                        })->where(function($query) use ($begin, $end)
                        {
                            $query->where('updated_at', ">", $begin)->where('updated_at', "<", $end);
                        })->count();
            }
        } else if ($type == "judgments")
        {
            if ($all_users == false)
            {
                $reports_count = Judgement::where(function($query) use ($user_id)
                        {
                            $query->where('created_by', $user_id)->orWhere('modified_by', $user_id);
                        })->where(function($query) use ($begin, $end)
                        {
                            $query->where('created_at', ">", $begin)->where('created_at', "<", $end);
                        })->count();
            } else
            {
                $reports_count = Judgement::where(function($query) use ($begin, $end)
                        {
                            $query->where('created_at', ">", $begin)->where('created_at', "<", $end);
                        })->count();
            }
        }

        return $reports_count;
    }

    public function ReadClerks()
    {
        $f = fopen("D:/clerks.csv", "r");
        while ($r = fgets($f))
        {
            $arr = explode(" ", iconv("Windows-1256", "UTF-8", trim($r)));

            $data[count($arr)][] = array(
                "name" => iconv("Windows-1256", "UTF-8", trim($r))
            );
        }

        fclose($f);

        foreach ($data[2] as $row)
        {
            $arr = explode(" ", $data[2]);
            if (isset($data[$judge->id]))
            {
                $judge->residence = $data[$judge->id]["residence"];
                $judge->save();
            }
        }
    }

    public function RegisterTable()
    {
        $stats = new Collection();
        $time_range = GetRange();

        $courts = judgeCourt::select("judge_courts.id as judge_court_id", "judge_courts.court_id", "courts.id as court_id", "courts.title", "judges.id as judge_id", "judges.first_name", "judges.last_name")
                        ->join('courts', 'courts.id', '=', 'court_id')
                        ->join('judges', 'judges.id', '=', 'judge_id')
                        ->whereIn('Courts.province_id', explode(",", Session::get('provinces')))
                        ->get()->keyBy('judge_court_id')->toArray();

        $register = judgeCourt::with(array('monthlyReport' => function($query) use ($time_range)
                    {
                        $query->select(['id', 'judge_court_id', 'month', 'year'])->groupBy('speciality_id', 'judge_court_id', 'month', 'year')
                                ->where(function($query) use ($time_range)
                                {
                                    $query
                                    ->where(function($query) use ($time_range)
                                    {
                                        $query->where('month', '>', 8)
                                        ->where('month', '<', 13)
                                        ->where('year', '=', date('Y', strtotime($time_range['from'])));
                                    })
                                    ->orwhere(function($query) use ($time_range)
                                    {
                                        $query->where('month', '>', 0)
                                        ->where('month', '<', 9)
                                        ->where('year', '=', date('Y', strtotime($time_range['to'])));
                                    })->groupBy("month");
                                });
                    }))->whereIn('id', array_keys($courts))->get(['id'])->toArray();

                foreach ($register as $judge_court)
                {

                    $title = $courts[$judge_court["id"]]["title"];
                    $judge_fname = $courts[$judge_court["id"]]["first_name"];
                    $judge_lname = $courts[$judge_court["id"]]["last_name"];

                    if (empty($title) || empty($judge_fname))
                        continue;

                    $record = [];
                    $record[0] = "<a href='" . url('profile', $courts[$judge_court['id']]['judge_id']) . "'>" . $title . " (" . $judge_fname . " " . $judge_lname . ")" . "</a>";

                    if (!empty($judge_court["monthly_report"]))
                    {
                        foreach ($judge_court["monthly_report"] as $monthly_report)
                        {
                            $index = (( date('n', strtotime($monthly_report['year'] . "/" . $monthly_report['month'] . "/01")) + 4 ) % 12);
                            if ($index != 0)
                            {
                                $record[$index] = '<span style="display:none;">1</span><a href="' . url('monthly_reports/' . $judge_court["id"] . "/" . $monthly_report["month"] . "/" . $monthly_report["year"]) . '">'
                                        . '<span class="glyphicon glyphicon-ok" style="color:green;" aria-hidden="true">'
                                        . '</span></a>';
                            }
                            else
                            {
                                $record[12] = '<span style="display:none;">1</span><a href="' . url('monthly_reports/' . $judge_court["id"] . "/" . $monthly_report["month"] . "/" . $monthly_report["year"]) . '">'
                                        . '<span class="glyphicon glyphicon-ok" style="color:green;" aria-hidden="true">'
                                        . '</span></a>';
                            }
                        }
                    }

                    for ($i = 0; $i <= 12; $i++)
                    {
                        if (!isset($record[$i]))
                            $record[$i] = '<span style="display:none;">0</span><a href="' . url('monthly_reports/' . $judge_court["id"] . "/" . getMonthByIndex($i - 1) . "/" . getYearByMonthIndex($i - 1)) . '"><span class="glyphicon glyphicon-remove" style="color:red;" aria-hidden="true">'
                                    . '</span></a>';
                    }

                    $stats->push($record);
                }

                return Datatables::of($stats)->make(true);
            }

        }
        