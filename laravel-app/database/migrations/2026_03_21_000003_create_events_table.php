<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedTinyInteger('cat_creneau')->default(1);
            $table->unsignedInteger('id_in_day')->nullable();
            $table->string('name');
            $table->text('description')->nullable();
            $table->dateTime('start');
            $table->dateTime('end')->nullable();
            $table->boolean('public')->default(false);
            $table->index(['start', 'cat_creneau']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
