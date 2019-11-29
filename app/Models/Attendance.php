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
     * 日付とユーザーIDによる勤怠情報取得
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $id
     * @param string $day
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFetchAttendance($query, $id, $day)
    {
        return $query->where('user_id', $id)
                     ->where('date', $day);
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
