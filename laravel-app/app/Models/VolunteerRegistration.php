<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class VolunteerRegistration extends Pivot
{
    protected $table = 'inscription_creneau';
    protected $primaryKey = 'id_inscription';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = ['id_user', 'id_event', 'fonction'];
}
