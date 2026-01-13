<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiaryEntry extends Model
{
    protected $fillable = [
        'user_id',
        'date',
        'content',
        'mood_id',
        'metadata',
    ];

    protected $casts = [
        'date' => 'date',
        'metadata' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function mood()
    {
        return $this->belongsTo(Mood::class);
    }

    public function interactions()
    {
        return $this->hasMany(EntryInteraction::class, 'entry_id');
    }

    public function photos()
    {
        return $this->hasMany(EntryPhoto::class, 'entry_id');
    }
}
