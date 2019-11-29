<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{

    public $timestamps = false;
    protected $table = 'attendance';

    protected $fillable = [
        'user_id',
        'is_absent',
        'is_request',
        'absent_reason',
        'request_reason',
        'date',
        'start_time',
        'end_time'
    ];
    
    protected $dates = [
        'date',
        'start_time',
        'end_time'
    ];

    protected $casts = [
        'is_absent' => 'boolean',
        'is_request' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 日付とユーザーIDによる勤怠情報
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $id
     * @param string $day
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearchAttendance($query, $id, $date)
    {
        return $query->where('user_id', $id)
                     ->where('date', $date);
    }

    /**
     * 今日の勤怠情報を取得
     *
     * @param int $id
     * @param string $date
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function fetchAttendance($id, $date)
    {
        return $this->searchAttendance($id, $date)
                    ->first();
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
        $this->updateOrCreate(
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
        $this->searchAttendance($datas['user_id'], $date)
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
