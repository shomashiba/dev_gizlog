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

    ];
}

