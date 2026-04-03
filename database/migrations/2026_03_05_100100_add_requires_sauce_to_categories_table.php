<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->boolean('requires_sauce')->default(false)->after('name_ar');
        });

        DB::table('categories')
            ->whereRaw('LOWER(name) = ?', ['appetizer'])
            ->orWhereRaw('LOWER(name_ar) = ?', ['مقبلات'])
            ->update(['requires_sauce' => true]);
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('requires_sauce');
        });
    }
};
