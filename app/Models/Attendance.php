<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
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

    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
