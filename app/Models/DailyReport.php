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
        'reporting_time',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'reporting_time',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function fetchAllDailyReports($userId)
    {
        return $this->filterEqual('user_id', $userId)
                    ->orderby('reporting_time', 'desc')
                    ->get();
    }

    public function fetchSearchingDailyReports($userId, $conditions)
    {
        return $this->filterLike('reporting_time', $conditions['search-month'])
                    ->filterEqual('user_id', $userId)
                    ->orderby('reporting_time', 'desc')
                    ->get();
    }
}
