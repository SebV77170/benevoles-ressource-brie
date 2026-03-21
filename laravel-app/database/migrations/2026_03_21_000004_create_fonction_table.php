<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('fonction', function (Blueprint $table) {
            $table->increments('id');
            $table->string('fonction');
            $table->string('description')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fonction');
    }
};
