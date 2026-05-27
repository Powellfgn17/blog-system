<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Models\Category;
use App\Models\Post;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PostController extends Controller
{
    public function __construct(private NotificationService $notifications) {}

    public function blogIndex(Request $request): View
    {
        $posts = Post::query()
            ->blog()
            ->with(['user', 'category'])
            ->recent()
            ->paginate(10);

        $categories = Category::query()->orderBy('name')->get();

        return view('blog.index', compact('posts', 'categories'));
    }

    public function communityIndex(Request $request): View
    {
        $posts = Post::query()
            ->community()
            ->with(['user', 'category'])
            ->recent()
            ->paginate(10);

        $categories = Category::query()->orderBy('name')->get();

        return view('community.index', compact('posts', 'categories'));
    }

    public function show(Post $post): View
    {
        $post->load(['user', 'category']);

        $post->load([
            'rootComments' => fn ($q) => $q->with(['user', 'replies.user', 'reactions']),
            'reactions',
        ]);

        $view = $post->isBlog() ? 'posts.show' : 'posts.show';

        return view($view, [
            'post' => $post,
            'context' => $post->isBlog() ? 'blog' : 'community',
        ]);
    }

    public function create(Request $request): View
    {
        $type = $this->resolveTypeFromRoute($request);

        if ($type === Post::TYPE_BLOG) {
            $this->authorize('createBlog', Post::class);
        } else {
            $this->authorize('createCommunity', Post::class);
        }

        $categories = Category::query()->orderBy('name')->get();

        return view($type === Post::TYPE_BLOG ? 'blog.create' : 'community.create', [
            'post' => new Post(['type' => $type]),
            'categories' => $categories,
            'type' => $type,
        ]);
    }

    public function edit(Request $request, Post $post): View
    {
        $this->authorize('update', $post);

        $categories = Category::query()->orderBy('name')->get();

        return view($post->isBlog() ? 'blog.create' : 'community.create', [
            'post' => $post,
            'categories' => $categories,
            'type' => $post->type,
        ]);
    }

    public function store(StorePostRequest $request): RedirectResponse
    {
        $type = $this->resolveTypeFromRoute($request);

        if ($type === Post::TYPE_BLOG) {
            $this->authorize('createBlog', Post::class);
        } else {
            $this->authorize('createCommunity', Post::class);
        }

        $post = Post::create([
            'user_id' => $request->user()->id,
            'category_id' => $request->validated('category_id'),
            'type' => $type,
            'title' => $request->validated('title'),
            'body' => $request->validated('body'),
        ]);

        $this->notifications->notifyMentions($post->body, $post, $post);

        return redirect()
            ->to($this->postShowRoute($post))
            ->with('success', 'Publication créée avec succès.');
    }

    public function update(StorePostRequest $request, Post $post): RedirectResponse
    {
        $this->authorize('update', $post);

        $post->update($request->validated());

        $this->notifications->notifyMentions($post->body, $post, $post);

        return redirect()
            ->to($this->postShowRoute($post))
            ->with('success', 'Publication mise à jour.');
    }

    public function destroy(Post $post): RedirectResponse
    {
        $this->authorize('delete', $post);

        $type = $post->type;
        $post->delete();

        return redirect()
            ->route($type === Post::TYPE_BLOG ? 'blog.index' : 'community.index')
            ->with('success', 'Publication supprimée.');
    }

    public function search(Request $request): View
    {
        $query = trim((string) $request->get('q', ''));

        $posts = Post::query()
            ->when($query !== '', function ($q) use ($query) {
                $q->where(function ($builder) use ($query) {
                    $builder->where('title', 'like', "%{$query}%")
                        ->orWhere('body', 'like', "%{$query}%");
                });
            })
            ->with(['user', 'category'])
            ->recent()
            ->paginate(10)
            ->withQueryString();

        return view('posts.search', compact('posts', 'query'));
    }

    private function resolveTypeFromRoute(Request $request): string
    {
        if ($request->routeIs('blog.*')) {
            return Post::TYPE_BLOG;
        }

        return Post::TYPE_COMMUNITY;
    }

    private function postShowRoute(Post $post): string
    {
        return $post->isBlog()
            ? route('blog.show', $post)
            : route('community.show', $post);
    }
}
