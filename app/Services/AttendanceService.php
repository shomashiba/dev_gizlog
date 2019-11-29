<?php

namespace App\Services;

use Carbon\Carbon;

const DATE = 'Y-m-d';
const CONVERT_HOURS = 60;

class AttendanceService
{
    /**
     * 今日の日付
     * @var string $today
     */
    public $today;

    public function __construct()
    {
        $this->today = Carbon::today()->format(DATE);
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

        if (empty($data->start_time) && empty($data->end_time)) {
            return 'not_attend';
        }

        if (!empty($data->start_time) && empty($data->end_time)) {
            return 'attend';
        }

        if (!empty($data->start_time) && !empty($data->end_time)) {
            return 'leave';
        }
    }
}