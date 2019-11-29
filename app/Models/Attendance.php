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
}
