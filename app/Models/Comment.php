<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Services\SearchingScope;
use App\Models\User;

class Comment extends Model
{
    use SoftDeletes, SearchingScope;

    protected $fillable = [
        'user_id',
        'questions_id',
        'comment',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function Question()
    {
        return $this->belongsTo('App\Models\Question');
    }
}