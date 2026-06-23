<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('short_links', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 32)->unique();
            $table->string('destination_url', 2048);
            $table->string('title')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
        });

        Schema::create('link_clicks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('short_link_id')->constrained('short_links')->cascadeOnDelete();
            $table->dateTime('clicked_at');
            $table->string('ip', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('referer', 2048)->nullable();

            $table->index(['short_link_id', 'clicked_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('link_clicks');
        Schema::dropIfExists('short_links');
    }
};
