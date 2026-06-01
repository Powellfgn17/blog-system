<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->enum('status', ['draft', 'published', 'scheduled'])->default('draft')->after('type');
            $table->timestamp('published_at')->nullable()->after('status');
            $table->boolean('is_featured')->default(false)->after('published_at');
            $table->string('slug')->unique()->nullable()->after('is_featured');
            $table->text('meta_description')->nullable()->after('slug');
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn(['status', 'published_at', 'is_featured', 'slug', 'meta_description']);
        });
    }
};
