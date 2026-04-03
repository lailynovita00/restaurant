<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('orders', 'user_id')) {
            return;
        }

        $dbName = DB::getDatabaseName();

        $constraints = DB::select(
            "SELECT CONSTRAINT_NAME
             FROM information_schema.KEY_COLUMN_USAGE
             WHERE TABLE_SCHEMA = ?
               AND TABLE_NAME = 'orders'
               AND COLUMN_NAME = 'user_id'
               AND REFERENCED_TABLE_NAME IS NOT NULL",
            [$dbName]
        );

        foreach ($constraints as $constraint) {
            DB::statement("ALTER TABLE `orders` DROP FOREIGN KEY `{$constraint->CONSTRAINT_NAME}`");
        }

        Schema::table('orders', function (Blueprint $table) {
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        if (!Schema::hasColumn('orders', 'user_id')) {
            return;
        }

        $dbName = DB::getDatabaseName();

        $constraints = DB::select(
            "SELECT CONSTRAINT_NAME
             FROM information_schema.KEY_COLUMN_USAGE
             WHERE TABLE_SCHEMA = ?
               AND TABLE_NAME = 'orders'
               AND COLUMN_NAME = 'user_id'
               AND REFERENCED_TABLE_NAME IS NOT NULL",
            [$dbName]
        );

        foreach ($constraints as $constraint) {
            DB::statement("ALTER TABLE `orders` DROP FOREIGN KEY `{$constraint->CONSTRAINT_NAME}`");
        }

        if (Schema::hasTable('customers')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->foreign('user_id')
                    ->references('id')
                    ->on('customers')
                    ->onDelete('cascade');
            });
        }
    }
};

