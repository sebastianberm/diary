<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EntryInteraction extends Model
{
    protected $fillable = [
        'entry_id',
        'person_id',
        'source',
    ];

    public function entry()
    {
        return $this->belongsTo(DiaryEntry::class, 'entry_id');
    }

    public function person()
    {
        return $this->belongsTo(Person::class);
    }
}
