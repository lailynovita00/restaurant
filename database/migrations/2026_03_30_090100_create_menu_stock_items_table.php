<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menu_stock_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_id')->constrained()->cascadeOnDelete();
            $table->foreignId('stock_item_id')->constrained()->cascadeOnDelete();
            $table->decimal('quantity_required', 12, 3);
            $table->timestamps();

            $table->unique(['menu_id', 'stock_item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_stock_items');
    }
};