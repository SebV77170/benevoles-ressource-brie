<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class EventSlot extends Model
{
    protected $table = 'events';
    public $timestamps = false;

    protected $fillable = ['cat_creneau', 'id_in_day', 'name', 'description', 'start', 'end', 'public'];

    protected function casts(): array
    {
        return [
            'start' => 'datetime',
            'end' => 'datetime',
            'public' => 'boolean',
            'cat_creneau' => 'integer',
        ];
    }

    public function volunteers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'inscription_creneau', 'id_event', 'id_user')
            ->using(VolunteerRegistration::class)
            ->withPivot(['id_inscription', 'fonction']);
    }
}
