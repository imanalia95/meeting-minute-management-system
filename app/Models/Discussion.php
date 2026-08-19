<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Discussion extends Model
{
    use HasFactory;

    protected $fillable = [
        'agenda_item_id',
        'summary',
        'decision',
    ];

    public function agendaItem(): BelongsTo
    {
        return $this->belongsTo(AgendaItem::class);
    }

    /** Optional — an action item may or may not trace back to a specific discussion */
    public function actionItems(): HasMany
    {
        return $this->hasMany(ActionItem::class);
    }
}
