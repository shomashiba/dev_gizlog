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

    public function fetchQuestion()
    {
        return $this->orderby('created_at', 'desc')
                    ->get();
    }

    public function fetchSearchingQuestion($conditions)
    {
        return $this->filterLike('title', $conditions['search_word'])
                    ->orderby('created_at', 'desc')
                    ->get();
    }

    public function fetchPersonalQuestion($userId)
    {
        return $this->where('user_id', $userId)
                    ->orderBy('created_at', 'desc')
                    ->get();
    }

    public function fetchTagQuestion($conditions)
    {
        return $this->filterEqual('tag_category_id', $conditions['tag_category_id'])
                    ->orderBy('created_at', 'desc')
                    ->get();
    }
    public function fetchSearchTagQuestion($conditions)
    {
        return $this->filterEqual('tag_category_id', $conditions['tag_category_id'])
                    ->filterLike('title', $conditions['search_word'])
                    ->orderBy('created_at', 'desc')
                    ->get();
    }
}
