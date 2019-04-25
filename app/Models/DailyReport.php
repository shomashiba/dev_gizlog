<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Services\SearchingScope;

class DailyReport extends Model
{
    use SoftDeletes, SearchingScope;

    protected $fillable = [
        'user_id',
        'title',
        'content',
        'answer',
        'reporting_time',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'reporting_time',
    ];
}
