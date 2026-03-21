<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'uuid_user';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'prenom', 'nom', 'pseudo', 'mail', 'tel', 'password', 'admin',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'admin' => 'integer',
            'password' => 'hashed',
        ];
    }

    public function getAuthPasswordName(): string
    {
        return 'password';
    }

    public function profileDates(): HasOne
    {
        return $this->hasOne(UserDate::class, 'id_user', 'uuid_user');
    }

    public function eventSlots(): BelongsToMany
    {
        return $this->belongsToMany(EventSlot::class, 'inscription_creneau', 'id_user', 'id_event')
            ->using(VolunteerRegistration::class)
            ->withPivot(['id_inscription', 'fonction'])
            ->orderBy('start');
    }

    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class, 'uuid_user', 'uuid_user');
    }

    public function isPending(): bool
    {
        return $this->admin === 0;
    }

    public function isManager(): bool
    {
        return $this->admin >= 1;
    }

    public function isAdministrator(): bool
    {
        return $this->admin >= 2;
    }

    public function getEmailForPasswordReset(): string
    {
        return $this->mail ?? '';
    }
}
