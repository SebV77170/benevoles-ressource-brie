<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('uuid_user');
            $table->string('prenom');
            $table->string('nom');
            $table->string('pseudo')->unique();
            $table->string('password');
            $table->unsignedTinyInteger('admin')->default(0);
            $table->string('mail')->nullable()->index();
            $table->string('tel')->nullable();
            $table->rememberToken();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
