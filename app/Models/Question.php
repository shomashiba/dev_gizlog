<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Services\SearchingScope;
use App\Models\User;

class Question extends Model
{
    use SoftDeletes, SearchingScope;

    protected $fillable = [
        'user_id',
        'tag_category_id',
        'title',
        'content',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function Comment()
    {
        return $this->hasMany('App\Models\Comment');
    }

    public function TagCategory()
    {
        return $this->belongsTo('App\Models\TagCategory');
    }

    public function fetchQuestion($userId)
    {
        return $this->where('user_id', $userId)
                    ->orderby('created_at', 'desc')
                    ->get();
    }
}

