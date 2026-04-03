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
            $table->boolean('requires_side')->default(false)->after('requires_sauce');
        });

        DB::table('categories')
            ->whereRaw('LOWER(name) = ?', ['main dishes'])
            ->update(['requires_side' => true]);
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('requires_side');
        });
    }
};
