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
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->morphs('commentable'); // Creates commentable_type and commentable_id columns + index
            $table->text('body');
            $table->unsignedTinyInteger('rating')->nullable()->comment('1-5 star rating, null if no rating given');
            $table->boolean('is_approved')->default(true)->comment('For future moderation feature');
            $table->timestamps();

            // Index for filtering
            $table->index('is_approved');
            $table->index('rating');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
