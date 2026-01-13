<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    protected $fillable = [
        'name',
        'type',
        'is_own_child',
        'immich_person_id',
        'keywords',
    ];

    protected $casts = [
        'is_own_child' => 'boolean',
        'keywords' => 'array',
    ];

    public function interactions()
    {
        return $this->hasMany(EntryInteraction::class);
    }

    public function childrenLogs()
    {
        return $this->hasMany(ChildrenLog::class);
    }
}
