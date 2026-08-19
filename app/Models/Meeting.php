<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Meeting extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'meeting_date',
        'start_time',
        'end_time',
        'location',
        'next_meeting_date',
        'status',
        'chairperson_id',
        'secretary_id',
        'created_by',
        'raw_notes',
        'ai_summary',
        'ai_summary_status',
        'ai_summary_generated_at',
    ];

    protected function casts(): array
    {
        return [
            'meeting_date' => 'date',
            'next_meeting_date' => 'date',
            'ai_summary_generated_at' => 'datetime',
        ];
    }

    // ── Relationships ──────────────────────────────────────────────

    public function chairperson(): BelongsTo
    {
        return $this->belongsTo(User::class, 'chairperson_id');
    }

    public function secretary(): BelongsTo
    {
        return $this->belongsTo(User::class, 'secretary_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function attendees(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'meeting_attendees')
            ->withPivot(['attendance_status', 'role_in_meeting'])
            ->withTimestamps();
    }

    public function agendaItems(): HasMany
    {
        return $this->hasMany(AgendaItem::class)->orderBy('sequence');
    }

    public function actionItems(): HasMany
    {
        return $this->hasMany(ActionItem::class);
    }

    // ── Convenience scopes ──────────────────────────────────────────

    public function scopeUpcoming($query)
    {
        return $query->where('meeting_date', '>=', now()->toDateString())
            ->orderBy('meeting_date');
    }

    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }
}
