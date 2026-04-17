<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('complaint_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('filename');
            $table->string('path');
            $table->string('mime_type');
            $table->unsignedBigInteger('size');
            $table->timestamps();

            $table->index('complaint_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attachments');
    }
};
