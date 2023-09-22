<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
    ];

    protected $casts =[];


    /**
     * post belongs to user
     * @return BelongsTo
     */
    function user():BelongsTo
    {
        return  $this->belongsTo(User::class);
    }
}
