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
            $table->string('slug')->unique();
            $table->string('image')->nullable();
            $table->text('description')->nullable();
            $table->unsignedInteger('price_in_cents');
            $table->unsignedInteger('purchase_price_in_cents');

            $table->decimal('margin_percentage')->default(0);
            $table->decimal('suggested_discount_percentage')->default(0);
            $table->unsignedInteger('discounted_price_in_cents')->default(0);
            $table->decimal('new_margin_percentage')->default(0);

            $table->timestamps();
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
