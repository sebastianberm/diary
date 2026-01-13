<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChildrenLog extends Model
{
    protected $fillable = [
        'date',
        'person_id',
        'status',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function person()
    {
        return $this->belongsTo(Person::class);
    }
}
