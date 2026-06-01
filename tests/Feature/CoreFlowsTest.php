<?php

namespace Tests\Feature;

use App\Events\CommentPosted;
use App\Models\Comment;
use App\Models\Notification;
use App\Models\Post;
use App\Models\Reaction;
use App\Models\Report;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CoreFlowsTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_comment_on_post(): void
    {
        Event::fake([CommentPosted::class]);

        $user = User::factory()->create();
        $post = Post::factory()->community()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('comments.store', $post), [
                'body' => 'Nouveau commentaire',
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'post_id' => $post->id,
            'body' => 'Nouveau commentaire',
        ]);
    }

    public function test_comment_reply_must_target_same_post(): void
    {
        Event::fake([CommentPosted::class]);

        $user = User::factory()->create();
        $post = Post::factory()->community()->create();
        $otherPost = Post::factory()->community()->create();
        $foreignParent = Comment::factory()->for($otherPost)->create();

        $response = $this
            ->actingAs($user)
            ->post(route('comments.store', $post), [
                'body' => 'Réponse invalide',
                'parent_id' => $foreignParent->id,
            ]);

        $response->assertStatus(422);
    }

    public function test_reaction_toggle_returns_json_counts(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->community()->create();

        $response = $this
            ->actingAs($user)
            ->postJson(route('reactions.toggle'), [
                'reactable_type' => 'post',
                'reactable_id' => $post->id,
                'type' => Reaction::TYPE_LIKE,
            ]);

        $response
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('counts.like', 1)
            ->assertJsonPath('user_reaction', Reaction::TYPE_LIKE);

        $this->assertDatabaseHas('reactions', [
            'user_id' => $user->id,
            'reactable_id' => $post->id,
            'reactable_type' => $post->getMorphClass(),
            'type' => Reaction::TYPE_LIKE,
        ]);
    }

    public function test_bookmark_toggle_creates_and_deletes_bookmark(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->community()->create();

        $create = $this->actingAs($user)->postJson(route('bookmarks.toggle', $post));
        $create->assertOk()->assertJsonPath('bookmarked', true);
        $this->assertDatabaseHas('bookmarks', ['user_id' => $user->id, 'post_id' => $post->id]);

        $delete = $this->actingAs($user)->postJson(route('bookmarks.toggle', $post));
        $delete->assertOk()->assertJsonPath('bookmarked', false);
        $this->assertDatabaseMissing('bookmarks', ['user_id' => $user->id, 'post_id' => $post->id]);
    }

    public function test_user_cannot_report_same_content_twice(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->community()->create();

        $this->actingAs($user)->post(route('reports.store'), [
            'reportable_type' => 'post',
            'reportable_id' => $post->id,
            'reason' => Report::REASON_SPAM,
        ])->assertRedirect();

        $this->actingAs($user)->post(route('reports.store'), [
            'reportable_type' => 'post',
            'reportable_id' => $post->id,
            'reason' => Report::REASON_OFFENSIVE,
        ])->assertRedirect()->assertSessionHas('error');

        $this->assertDatabaseCount('reports', 1);
    }

    public function test_user_can_mark_notification_as_read_via_json(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->community()->create();
        $notification = Notification::create([
            'user_id' => $user->id,
            'type' => Notification::TYPE_COMMENT,
            'notifiable_type' => $post->getMorphClass(),
            'notifiable_id' => $post->id,
            'data' => ['url' => route('community.show', $post)],
        ]);

        $response = $this
            ->actingAs($user)
            ->postJson(route('notifications.read', $notification));

        $response
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('unread_count', 0);

        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_user_cannot_mark_another_users_notification_as_read(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $post = Post::factory()->community()->create();
        $notification = Notification::create([
            'user_id' => $otherUser->id,
            'type' => Notification::TYPE_COMMENT,
            'notifiable_type' => $post->getMorphClass(),
            'notifiable_id' => $post->id,
            'data' => ['url' => route('community.show', $post)],
        ]);

        $this->actingAs($user)
            ->postJson(route('notifications.read', $notification))
            ->assertForbidden();
    }

    public function test_admin_routes_are_forbidden_for_non_admin_users(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $this->actingAs($user)
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }

    public function test_comment_update_supports_json_response(): void
    {
        $user = User::factory()->create();
        $comment = Comment::factory()->for($user)->create(['body' => 'Ancien texte']);

        $response = $this
            ->actingAs($user)
            ->putJson(route('comments.update', $comment), [
                'body' => 'Texte mis a jour',
            ]);

        $response
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('comment.body', 'Texte mis a jour');
    }

    public function test_report_store_supports_json_conflict_response_on_duplicate(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->community()->create();

        $this->actingAs($user)->postJson(route('reports.store'), [
            'reportable_type' => 'post',
            'reportable_id' => $post->id,
            'reason' => Report::REASON_SPAM,
        ])->assertOk();

        $this->actingAs($user)->postJson(route('reports.store'), [
            'reportable_type' => 'post',
            'reportable_id' => $post->id,
            'reason' => Report::REASON_SPAM,
        ])->assertStatus(409)->assertJsonPath('success', false);
    }

    public function test_comment_store_is_rate_limited_after_limit(): void
    {
        Event::fake([CommentPosted::class]);

        $user = User::factory()->create();
        $post = Post::factory()->community()->create();

        for ($i = 0; $i < 20; $i++) {
            $this->actingAs($user)->postJson(route('comments.store', $post), [
                'body' => "Comment {$i}",
            ])->assertOk();
        }

        $this->actingAs($user)->postJson(route('comments.store', $post), [
            'body' => 'Comment limite',
        ])->assertStatus(429);
    }
}
