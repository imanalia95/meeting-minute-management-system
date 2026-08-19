<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'position',
        'department',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // ── Relationships ──────────────────────────────────────────────

    /** Meetings this user created */
    public function createdMeetings(): HasMany
    {
        return $this->hasMany(Meeting::class, 'created_by');
    }

    /** Meetings this user chaired */
    public function chairedMeetings(): HasMany
    {
        return $this->hasMany(Meeting::class, 'chairperson_id');
    }

    /** Meetings this user took minutes for */
    public function secretariedMeetings(): HasMany
    {
        return $this->hasMany(Meeting::class, 'secretary_id');
    }

    /** Meetings this user attended (via pivot) */
    public function attendedMeetings(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Meeting::class, 'meeting_attendees')
            ->withPivot(['attendance_status', 'role_in_meeting'])
            ->withTimestamps();
    }

    /** Action items assigned to this user */
    public function assignedActionItems(): HasMany
    {
        return $this->hasMany(ActionItem::class, 'assigned_to');
    }
}
