<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('complaints', function (Blueprint $table) {
            $table->foreignId('user_id')
                ->after('id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('assigned_to')
                ->nullable()
                ->after('status')
                ->constrained('users')
                ->nullOnDelete();

            $table->index(['status', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::table('complaints', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['assigned_to']);
            $table->dropIndex(['status', 'user_id']);
            $table->dropColumn(['user_id', 'assigned_to']);
        });
    }
};
