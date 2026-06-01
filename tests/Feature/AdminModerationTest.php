<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Post;
use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminModerationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_moderation_page_with_users_list(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        User::factory()->count(3)->create();

        $this->actingAs($admin)
            ->get(route('admin.moderation'))
            ->assertOk()
            ->assertSee('Liste utilisateurs');
    }

    public function test_non_admin_cannot_access_moderation_routes(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $this->actingAs($user)
            ->get(route('admin.moderation'))
            ->assertForbidden();
    }

    public function test_admin_can_block_and_unblock_regular_user(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $target = User::factory()->create(['is_admin' => false, 'is_blocked' => false]);

        $this->actingAs($admin)
            ->post(route('admin.users.block', $target))
            ->assertRedirect();

        $this->assertTrue($target->fresh()->is_blocked);

        $this->actingAs($admin)
            ->post(route('admin.users.unblock', $target))
            ->assertRedirect();

        $this->assertFalse($target->fresh()->is_blocked);
    }

    public function test_admin_cannot_block_self_or_other_admin(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $otherAdmin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->from(route('admin.moderation'))
            ->post(route('admin.users.block', $admin))
            ->assertRedirect(route('admin.moderation'))
            ->assertSessionHas('error');

        $this->actingAs($admin)
            ->from(route('admin.moderation'))
            ->post(route('admin.users.block', $otherAdmin))
            ->assertRedirect(route('admin.moderation'))
            ->assertSessionHas('error');

        $this->assertFalse($admin->fresh()->is_blocked);
        $this->assertFalse($otherAdmin->fresh()->is_blocked);
    }

    public function test_admin_can_ignore_pending_reports_for_content(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $author = User::factory()->create();
        $reporter = User::factory()->create();
        $post = Post::factory()->community()->for($author)->create();
        $comment = Comment::factory()->for($post)->for($author)->create();

        $report = Report::create([
            'user_id' => $reporter->id,
            'reportable_type' => $comment->getMorphClass(),
            'reportable_id' => $comment->id,
            'reason' => Report::REASON_OFFENSIVE,
            'status' => Report::STATUS_PENDING,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.reports.ignore', $report))
            ->assertRedirect();

        $this->assertDatabaseHas('reports', [
            'id' => $report->id,
            'status' => Report::STATUS_IGNORED,
        ]);
    }
}
