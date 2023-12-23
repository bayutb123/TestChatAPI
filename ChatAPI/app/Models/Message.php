<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    protected $fillable = [
        'chat_id',
        'sender_id',
        'content',
        'read',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */

    protected $hidden = [
        'deleted_at',
    ];

    /**
     * The attributes that should be cast.
     */

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
