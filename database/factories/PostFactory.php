<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition(): array
    {
        $body = fake()->paragraphs(asText: true);

        return [
            'user_id' => User::factory(),
            'category_id' => Category::factory(),
            'type' => fake()->randomElement([Post::TYPE_BLOG, Post::TYPE_COMMUNITY]),
            'title' => fake()->sentence(6),
            'body' => $body,
            'reading_time' => Post::calculateReadingTime($body),
        ];
    }

    public function blog(): static
    {
        return $this->state(fn () => ['type' => Post::TYPE_BLOG]);
    }

    public function community(): static
    {
        return $this->state(fn () => ['type' => Post::TYPE_COMMUNITY]);
    }
}

