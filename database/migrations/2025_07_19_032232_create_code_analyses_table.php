<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('code_analyses', function (Blueprint $table) {
            $table->id();
            $table->text('code');
            $table->text('analysis');
            $table->json('suggestions');
            $table->integer('score');
            $table->string('file_name')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('code_analyses');
    }
};
