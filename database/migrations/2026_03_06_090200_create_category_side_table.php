<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('category_side', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('side_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['category_id', 'side_id']);
        });

        $categoryIds = DB::table('categories')
            ->where('requires_side', true)
            ->pluck('id');

        $sideIds = DB::table('sides')->pluck('id');

        $now = now();
        $rows = [];

        foreach ($categoryIds as $categoryId) {
            foreach ($sideIds as $sideId) {
                $rows[] = [
                    'category_id' => $categoryId,
                    'side_id' => $sideId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        if (!empty($rows)) {
            DB::table('category_side')->insert($rows);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('category_side');
    }
};
