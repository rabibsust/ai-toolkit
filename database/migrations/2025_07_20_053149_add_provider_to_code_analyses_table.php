<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('code_analyses', function (Blueprint $table) {
            $table->string('provider')->default('gemini')->after('score');
            $table->string('model')->nullable()->after('provider');
            $table->decimal('cost', 8, 6)->nullable()->after('model');
            $table->integer('tokens_used')->nullable()->after('cost');
        });
    }

    public function down(): void
    {
        Schema::table('code_analyses', function (Blueprint $table) {
            $table->dropColumn(['provider', 'model', 'cost', 'tokens_used']);
        });
    }
};
