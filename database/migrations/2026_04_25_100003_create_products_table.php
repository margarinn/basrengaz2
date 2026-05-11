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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->unsignedBigInteger('price')->comment('Price in IDR (Rupiah), stored as integer');
            $table->string('image')->nullable()->comment('Path to product image');
            $table->boolean('is_active')->default(true)->comment('Controls visibility on public site');
            $table->unsignedInteger('sort_order')->default(0)->comment('Lower number = shown first');
            $table->timestamps();
            $table->softDeletes();

            // Indexes for common queries
            $table->index('is_active');
            $table->index('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
