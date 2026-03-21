<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserDate extends Model
{
    protected $table = 'date_users';
    protected $primaryKey = 'id_date';
    public $timestamps = false;

    protected $fillable = [
        'id_user', 'date_inscription', 'date_derniere_visite', 'date_dernier_creneau', 'date_prochain_creneau',
    ];

    protected function casts(): array
    {
        return [
            'date_inscription' => 'datetime',
            'date_derniere_visite' => 'datetime',
            'date_dernier_creneau' => 'datetime',
            'date_prochain_creneau' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'uuid_user');
    }
}
