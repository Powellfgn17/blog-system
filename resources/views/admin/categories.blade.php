@extends('layouts.app')

@section('content')
<main class="w-full grid grid-cols-1 md:grid-cols-12 gap-gutter">
    <!-- Header -->
    <div class="col-span-1 md:col-span-12 mb-8 flex flex-col md:flex-row justify-between items-start md:items-end border-b border-surface-border dark:border-surface-tint pb-4">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <h1 class="font-headline-lg-mobile md:font-headline-lg text-headline-lg-mobile md:text-headline-lg text-official-ink dark:text-paper-white">Categories</h1>
                <span class="font-ui-medium text-community-indigo border-l-2 border-surface-border pl-3 hidden md:inline-block">Admin Workspace</span>
            </div>
            <p class="font-ui-small text-ui-small text-on-surface-variant">Manage content categories for the platform.</p>
        </div>
        <div class="hidden md:flex space-x-4 mt-4 md:mt-0">
            <a href="{{ route('admin.dashboard') }}" class="font-ui-small text-ui-small border border-surface-border dark:border-surface-tint text-official-ink dark:text-paper-white px-4 py-2 rounded-DEFAULT hover:bg-surface-container dark:hover:bg-primary-container transition-colors flex items-center gap-2">
                <span class="material-symbols-outlined text-sm">arrow_back</span>
                Dashboard
            </a>
        </div>
    </div>

    <!-- Add Category Form -->
    <div class="col-span-1 md:col-span-12 mb-8">
        <section class="bg-surface-container-lowest dark:bg-primary-container/10 border border-surface-border dark:border-surface-tint rounded-lg shadow-[0px_4px_20px_rgba(15,23,42,0.05)] p-6">
            <h2 class="font-ui-medium text-ui-medium font-bold text-official-ink dark:text-paper-white flex items-center gap-2 mb-4">
                <span class="material-symbols-outlined text-community-teal">add_circle</span>
                Add New Category
            </h2>
            <form method="POST" action="{{ route('admin.categories.store') }}" class="flex gap-3">
                @csrf
                <div class="flex-grow relative">
                    <input
                        type="text"
                        name="name"
                        class="w-full bg-paper-white dark:bg-official-ink border border-surface-border dark:border-surface-tint rounded-DEFAULT px-4 py-2.5 font-ui-small text-ui-small text-official-ink dark:text-paper-white focus:border-community-teal focus:ring-1 focus:ring-community-teal transition-colors placeholder-on-surface-variant"
                        placeholder="Category name..."
                    >
                </div>
                <button class="bg-official-ink dark:bg-paper-white text-paper-white dark:text-official-ink px-6 py-2.5 rounded-DEFAULT font-ui-medium text-ui-medium hover:opacity-90 transition-opacity flex items-center gap-2 whitespace-nowrap">
                    <span class="material-symbols-outlined text-sm">add</span>
                    Add
                </button>
            </form>
        </section>
    </div>

    <!-- Categories Grid -->
    <div class="col-span-1 md:col-span-12">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
            @foreach($categories as $category)
                <div class="bg-surface-container-lowest dark:bg-primary-container/10 border border-surface-border dark:border-surface-tint rounded-lg p-5 flex items-center justify-between shadow-[0px_4px_20px_rgba(15,23,42,0.03)] hover:border-community-indigo/30 transition-colors group">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-surface-container dark:bg-primary-container rounded-lg flex items-center justify-center">
                            <span class="material-symbols-outlined text-community-indigo text-sm">label</span>
                        </div>
                        <div>
                            <p class="font-ui-medium text-ui-medium text-official-ink dark:text-paper-white font-semibold">{{ $category->name }}</p>
                            <p class="font-ui-small text-ui-small text-on-surface-variant text-xs">{{ $category->posts_count }} {{ $category->posts_count > 1 ? 'posts' : 'post' }}</p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('admin.categories.destroy', $category) }}">
                        @csrf @method('DELETE')
                        <button class="font-ui-small text-ui-small text-reaction-red hover:text-red-600 opacity-0 group-hover:opacity-100 transition-opacity flex items-center gap-1">
                            <span class="material-symbols-outlined text-sm">delete</span>
                        </button>
                    </form>
                </div>
            @endforeach
        </div>

        @if($categories->isEmpty())
            <div class="text-center py-16 bg-surface-container-lowest dark:bg-primary-container/10 border border-surface-border dark:border-surface-tint rounded-lg">
                <span class="material-symbols-outlined text-4xl text-surface-dim mb-2">category</span>
                <p class="font-ui-medium text-ui-medium text-on-surface-variant">No categories yet. Add one above.</p>
            </div>
        @endif
    </div>
</main>
@endsection
