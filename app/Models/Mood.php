<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mood extends Model
{
    protected $fillable = [
        'name',
        'icon',
    ];

    public function entries()
    {
        return $this->hasMany(DiaryEntry::class);
    }
}
