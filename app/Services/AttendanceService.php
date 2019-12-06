<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Attendance;

class AttendanceService
{
    const DATE = 'Y-m-d';
    const MINUTE = 60;
    const START_WORK = '09:00:00';

    public $today;

    public function __construct(Attendance $attendance)
    {
        $this->today = Carbon::today()->format(self::DATE);
        $this->attendance = $attendance;
    }

    /**
     * 出社日のみの合計学習時間の算出
     *
     * @param Illuminate\Support\Collection $attendance
     * @return int $totalStudyHours
     */
    public function calcStudyTime($attendances)
    {
        $totalStudyHours = 0;
        $filtered = $attendances->reject(function ($attendance) {
            return is_null($attendance->end_time);
        });

        foreach ($filtered as $attendance) {
            $studyMinutes = $attendance->start_time->diffInMinutes($attendance->end_time);
            $totalStudyHours += round($studyMinutes /self::MINUTE);
        }
        return $totalStudyHours;
    }

    /**
     * ユーザーの出退勤状態の判別
     *
     * @param Illuminate\Support\Collection $attendance
     * @return string
     */
    public function confirmAttendanceState($attendance)
    {
        if (!empty($attendance->is_absent)) {
            return 'absent';
        }

        if (!empty($attendance->start_time) && empty($attendance->end_time)) {
            if ($this->confirmOvertime()) {
                return 'attend';
            }
            return 'attend';
        }

        if (empty($attendance->start_time) && empty($attendance->end_time)) {
            return 'not_attend';
        }
        
        if (!empty($attendance->start_time) && !empty($attendance->end_time)) {
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
        $startWork = new Carbon(self::START_WORK);
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
        $yesterday = $date->subDay()->format(self::DATE);
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
     * @param array $attendance
     * @param string $date
     */
    public function storeAbsence($inputs, $date)
    {
        $inputs['is_absent'] = true;
        $this->attendance->updateOrCreate(
            [
                'user_id' => $inputs['user_id'],
                'date' => $date
            ],
            $inputs
        );
    }

    /**
     * 修正申請を保存
     *
     * @param array $attendance
     * @param string $date
     */
    public function storeModify($inputs, $date)
    {
        $inputs['is_request'] = true;
        $this->attendance->searchAttendance($inputs['user_id'], $date)
             ->update($inputs);
    }

    /**
     * ユーザーの勤怠情報を取得
     *
     * @param int $id
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function fetchMyAttendance($id)
    {
        return $this->attendance->where('user_id', $id)
                    ->latest('date')
                    ->get();
    }
}