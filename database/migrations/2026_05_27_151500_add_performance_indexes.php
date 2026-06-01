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
            $table->index(['is_admin', 'is_blocked'], 'users_admin_blocked_idx');
        });

        Schema::table('posts', function (Blueprint $table) {
            $table->index(['type', 'created_at'], 'posts_type_created_idx');
            $table->index(['user_id', 'created_at'], 'posts_user_created_idx');
            $table->index(['category_id', 'type', 'created_at'], 'posts_category_type_created_idx');
        });

        Schema::table('comments', function (Blueprint $table) {
            $table->index(['post_id', 'parent_id', 'created_at'], 'comments_post_parent_created_idx');
            $table->index(['user_id', 'created_at'], 'comments_user_created_idx');
        });

        Schema::table('reactions', function (Blueprint $table) {
            $table->index(['reactable_type', 'reactable_id', 'type'], 'reactions_reactable_type_idx');
            $table->index(['user_id', 'created_at'], 'reactions_user_created_idx');
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->index(['user_id', 'read_at', 'created_at'], 'notifications_user_read_created_idx');
            $table->index(['notifiable_type', 'notifiable_id'], 'notifications_notifiable_idx');
        });

        Schema::table('reports', function (Blueprint $table) {
            $table->index(['status', 'created_at'], 'reports_status_created_idx');
            $table->index(['reportable_type', 'reportable_id', 'status'], 'reports_reportable_status_idx');
        });

        Schema::table('bookmarks', function (Blueprint $table) {
            $table->index(['user_id', 'created_at'], 'bookmarks_user_created_idx');
            $table->index(['post_id', 'created_at'], 'bookmarks_post_created_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookmarks', function (Blueprint $table) {
            $table->dropIndex('bookmarks_user_created_idx');
            $table->dropIndex('bookmarks_post_created_idx');
        });

        Schema::table('reports', function (Blueprint $table) {
            $table->dropIndex('reports_status_created_idx');
            $table->dropIndex('reports_reportable_status_idx');
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex('notifications_user_read_created_idx');
            $table->dropIndex('notifications_notifiable_idx');
        });

        Schema::table('reactions', function (Blueprint $table) {
            $table->dropIndex('reactions_reactable_type_idx');
            $table->dropIndex('reactions_user_created_idx');
        });

        Schema::table('comments', function (Blueprint $table) {
            $table->dropIndex('comments_post_parent_created_idx');
            $table->dropIndex('comments_user_created_idx');
        });

        Schema::table('posts', function (Blueprint $table) {
            $table->dropIndex('posts_type_created_idx');
            $table->dropIndex('posts_user_created_idx');
            $table->dropIndex('posts_category_type_created_idx');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_admin_blocked_idx');
        });
    }
};
