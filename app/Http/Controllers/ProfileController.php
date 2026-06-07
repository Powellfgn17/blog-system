<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Bookmark;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show(string $username): View
    {
        $user = User::query()->where('username', $username)->firstOrFail();

        $posts = $user->communityPosts()
            ->with('category')
            ->recent()
            ->paginate(10);

        $postsCount = $user->posts()->community()->count();
        $commentsCount = $user->comments()->count();

        return view('profile.show', compact('user', 'posts', 'postsCount', 'commentsCount'));
    }

    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->fill($request->safe()->only(['name', 'bio']));

        if ($request->hasFile('avatar')) {
            \Cloudinary\Configuration\Configuration::instance([
                'cloud' => [
                    'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                    'api_key'    => env('CLOUDINARY_API_KEY'),
                    'api_secret' => env('CLOUDINARY_API_SECRET'),
                ],
                'url' => ['secure' => true]
            ]);

            $result = (new \Cloudinary\Api\Upload\UploadApi())->upload(
                $request->file('avatar')->getRealPath()
            );
            $user->avatar = $result['secure_url'];
        }

        if ($request->filled('password')) {
            $user->password = Hash::make($request->validated('password'));
        }

        $user->save();

        return Redirect::route('profile.edit')->with('success', 'Profil mis à jour.');
    }

    public function bookmarks(Request $request): View
    {
        $bookmarks = Bookmark::query()
            ->where('user_id', $request->user()->id)
            ->with(['post.user', 'post.category'])
            ->latest('created_at')
            ->paginate(10);

        return view('profile.bookmarks', compact('bookmarks'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
