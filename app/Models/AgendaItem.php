<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AgendaItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'meeting_id',
        'sequence',
        'title',
    ];

    public function meeting(): BelongsTo
    {
        return $this->belongsTo(Meeting::class);
    }

    /** 1:1 — each agenda item has exactly one discussion record */
    public function discussion(): HasOne
    {
        return $this->hasOne(Discussion::class);
    }
}
