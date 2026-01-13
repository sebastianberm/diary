<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EntryPhoto extends Model
{
    protected $fillable = [
        'entry_id',
        'immich_asset_id',
        'local_path',
        'caption',
        'taken_at',
    ];

    protected $casts = [
        'taken_at' => 'datetime',
    ];

    public function entry()
    {
        return $this->belongsTo(DiaryEntry::class, 'entry_id');
    }
}
