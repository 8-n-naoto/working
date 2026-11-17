<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('step_token_hash', 64)->nullable()->unique();
            $table->timestamp('step_token_last_used_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['step_token_hash']);
            $table->dropColumn(['step_token_hash', 'step_token_last_used_at']);
        });
    }
};
