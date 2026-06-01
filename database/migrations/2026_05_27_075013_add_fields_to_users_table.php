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
        Schema::table('users', function (Blueprint $table) {
            $table->string('username', 30)->unique()->after('id');
            $table->string('avatar')->nullable()->after('password');
            $table->text('bio')->nullable()->after('avatar');
            $table->boolean('is_admin')->default(false)->after('bio');
            $table->boolean('is_blocked')->default(false)->after('is_admin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'username',
                'avatar',
                'bio',
                'is_admin',
                'is_blocked',
            ]);
        });
    }
};
