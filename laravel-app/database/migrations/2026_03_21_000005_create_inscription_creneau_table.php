<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('inscription_creneau', function (Blueprint $table) {
            $table->increments('id_inscription');
            $table->foreignId('id_user')->constrained('users', 'uuid_user')->cascadeOnDelete();
            $table->foreignId('id_event')->constrained('events')->cascadeOnDelete();
            $table->string('fonction')->default('N/A');
            $table->unique(['id_user', 'id_event']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inscription_creneau');
    }
};
