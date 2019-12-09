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
     * 出社日のみ取得
     *
     * @param Illuminate\Support\Collection $attendances
     * @return Illuminate\Support\Collection
     */
    public function fetchOnlyAtWorkDay($attendances)
    {
        return $attendances->reject(function ($attendance) {
            return is_null($attendance->start_time) || is_null($attendance->end_time);
        });
    }

    /**
     * 出社日のみの学習日数のカウント
     *
     * @param Illuminate\Support\Collection $attendances
     * @return int
     */
    public function countStudyDays($attendances)
    {
        return $attendances->where('is_absent', false)
                           ->whereNotIn('end_time', '')
                           ->count();
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
        $filtered = $this->fetchOnlyAtWorkDay($attendances);

        foreach ($filtered as $attendance) {
            $studyMinutes = $attendance->start_time->diffInMinutes($attendance->end_time);
            $totalStudyHours += $studyMinutes;
        }
        return round($totalStudyHours / self::MINUTE);
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

        if (empty($attendance->start_time)) {
            return 'not_attend';
        }

        if (empty($attendance->end_time)) {
            return 'attend';
        }

        return 'leave';
    }

    /**
     * 残業判定。始業時間より前の場合、残業中。
     *
     * @return boolean
     */
    public function isOvertime()
    {
        $startWork = new Carbon(self::START_WORK);
        $currentTime = Carbon::now();
        return $currentTime->lt($startWork);
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
    public function fetchAttendance($id)
    {
        if ($this->isOvertime()) {
            return $this->attendance->searchAttendance($id, Carbon::yesterday())->first();
        }
        return $this->attendance->searchAttendance($id, $this->today)->first();
    }

    /**
     * 欠席情報を保存
     *
     * @param array $attendance
     * @param string $date
     */
    public function storeAbsence($inputs, $id)
    {
        $inputs['is_absent'] = true;
        $this->attendance->updateOrCreate(
            [
                'user_id' => $id,
                'date' => $this->today,
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
    public function storeModify($inputs, $id)
    {
        $inputs['is_requesting'] = true;
        $this->attendance->searchAttendance($id, $this->today)
                         ->update($inputs);
    }

    /**
     * ユーザーの勤怠情報を取得
     *
     * @param int $id
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function fetchMyAttendanceList($id)
    {
        return $this->attendance->where('user_id', $id)
                                ->latest('date')
                                ->get();
    }
}