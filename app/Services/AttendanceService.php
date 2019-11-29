<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Attendance;

const DATE = 'Y-m-d';
const CONVERT_HOURS = 60;
const START_WORK = '09:00:00';

class AttendanceService
{
    public $today;

    public function __construct(Attendance $attendance)
    {
        $this->today = Carbon::today()->format(DATE);
        $this->attendance = $attendance;
    }

    /**
     * 出社日のみの合計学習時間の算出
     *
     * @param Illuminate\Support\Collection $datas
     * @return int $totalStudyHours
     */
    public function calcStudyTime($datas)
    {
        $totalStudyHours = 0;
        $datas = $datas->whereNotIn('end_time', '')->all();

        foreach ($datas as $data) {
            $studyMinutes = $data->start_time->diffInMinutes($data->end_time);
            $totalStudyHours += round($studyMinutes / CONVERT_HOURS);
        }
        return $totalStudyHours;
    }

    /**
     * ユーザーの出退勤状態の判別
     *
     * @param Illuminate\Support\Collection $data
     * @return string
     */
    public function confirmAttendanceState($data)
    {
        if (!empty($data->is_absent)) {
            return 'absent';
        }

        if (!empty($data->start_time) && empty($data->end_time)) {
            if ($this->confirmOvertime()) {
                return 'attend';
            }
            return 'attend';
        }

        if (empty($data->start_time) && empty($data->end_time)) {
            return 'not_attend';
        }
        
        if (!empty($data->start_time) && !empty($data->end_time)) {
            return 'leave';
        }
    }

    /**
     * 残業判定。残業中ならtureを返す。
     *
     * @return boolean
     */
    public function confirmOvertime()
    {
        $startWork = new Carbon(START_WORK);
        $currentTime = Carbon::now();
        return ($currentTime < $startWork) ? true : false;
    }

    /**
     * 引数の日付の前日を取得
     *
     * @param string $date
     * @return string $yesterday
     */
    public function yesterday($date)
    {
        $date = new Carbon($date);
        $yesterday = $date->subDay();
        return $yesterday;
    }

    /**
     * 勤怠情報を取得
     * 
     * 残業状態をチェックし、昨日または今日の勤怠を取得する。
     *
     * @param int $id
     * @param string $date
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function fetchAttendance($id, $date)
    {
        if ($this->confirmOvertime()) {
            return $this->attendance->searchAttendance($id, $this->yesterday($date))->first();
        }
        return $this->attendance->searchAttendance($id, $date)->first();
    }

    /**
     * 欠席情報を保存
     *
     * @param array $datas
     * @param string $date
     */
    public function storeAbsence($datas, $date)
    {
        $datas['is_absent'] = true;
        $this->attendance->updateOrCreate(
            [
                'user_id' => $datas['user_id'],
                'date' => $date
            ],
            $datas
        );
    }

    /**
     * 修正申請を保存
     *
     * @param array $datas
     * @param string $date
     */
    public function storeModify($datas, $date)
    {
        $datas['is_request'] = true;
        $this->attendance->searchAttendance($datas['user_id'], $date)
             ->update($datas);
    }

    /**
     * ユーザーの勤怠情報を取得
     *
     * @param int $id
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function fetchMyAttendance($id)
    {
        return $this->where('user_id', $id)
                    ->latest('date')
                    ->get();
    }
}