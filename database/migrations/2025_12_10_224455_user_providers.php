<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        //
        Schema::create('user_providers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->string('provider_name', 50);
            $table->string('provider_id', 150);
            $table->string('provider_email', 150);
            $table->string('avatar_url')->nullable();
            $table->string('nickname')->nullable();
            $table->json('raw_profile')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'provider_name']);
            $table->unique(['provider_name', 'provider_id']);
            $table->unique(['provider_email', 'provider_id']);
            $table->index('provider_name');
            $table->index('provider_id');
            $table->index('provider_email');
            $table->index(['provider_name', 'provider_email']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('user_providers');
    }
};
