<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('category_sauce', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sauce_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['category_id', 'sauce_id']);
        });

        $appetizerIds = DB::table('categories')
            ->where('requires_sauce', true)
            ->pluck('id');

        $sauceIds = DB::table('sauces')->pluck('id');

        $now = now();
        $rows = [];

        foreach ($appetizerIds as $categoryId) {
            foreach ($sauceIds as $sauceId) {
                $rows[] = [
                    'category_id' => $categoryId,
                    'sauce_id' => $sauceId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        if (! empty($rows)) {
            DB::table('category_sauce')->insert($rows);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('category_sauce');
    }
};
