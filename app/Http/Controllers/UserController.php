<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function search(Request $request): JsonResponse
    {
        $query = trim((string) $request->get('q', ''));

        if (strlen($query) < 1) {
            return response()->json(['users' => []]);
        }

        $users = User::query()
            ->where('username', 'like', $query.'%')
            ->where('is_blocked', false)
            ->limit(10)
            ->get(['id', 'username', 'name', 'avatar']);

        return response()->json([
            'users' => $users->map(fn (User $user) => [
                'id' => $user->id,
                'username' => $user->username,
                'name' => $user->name,
                'avatar_url' => $user->avatar_url,
            ]),
        ]);
    }
}
