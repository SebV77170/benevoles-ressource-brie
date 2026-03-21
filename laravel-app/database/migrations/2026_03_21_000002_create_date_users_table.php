<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('date_users', function (Blueprint $table) {
            $table->increments('id_date');
            $table->foreignId('id_user')->constrained('users', 'uuid_user')->cascadeOnDelete();
            $table->dateTime('date_inscription');
            $table->dateTime('date_derniere_visite');
            $table->dateTime('date_dernier_creneau')->nullable();
            $table->dateTime('date_prochain_creneau')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('date_users');
    }
};
