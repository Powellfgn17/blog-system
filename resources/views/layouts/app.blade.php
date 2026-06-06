<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Thesaurus') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        "surface-variant": "#e4e2e4",
                        "on-primary-container": "#7c839b",
                        "official-ink": "#0F172A",
                        "on-primary-fixed-variant": "#3f465c",
                        "surface-container": "#f0edef",
                        "surface-dim": "#dcd9db",
                        "tertiary-container": "#271901",
                        "surface-border": "#E2E8F0",
                        "community-indigo": "#4F46E5",
                        "surface-container-lowest": "#ffffff",
                        "on-error-container": "#93000a",
                        "error-container": "#ffdad6",
                        "primary-container": "#131b2e",
                        "paper-white": "#F8FAFC",
                        "on-secondary-fixed": "#00201d",
                        "on-tertiary-container": "#98805d",
                        "outline": "#76777d",
                        "secondary-fixed-dim": "#6bd8cb",
                        "error": "#ba1a1a",
                        "on-tertiary": "#ffffff",
                        "tertiary": "#000000",
                        "on-secondary-fixed-variant": "#005049",
                        "tertiary-fixed": "#fcdeb5",
                        "on-tertiary-fixed-variant": "#574425",
                        "outline-variant": "#c6c6cd",
                        "secondary": "#006a61",
                        "on-background": "#1b1b1d",
                        "surface-container-high": "#eae7e9",
                        "inverse-primary": "#bec6e0",
                        "inverse-surface": "#303032",
                        "primary": "#000000",
                        "inverse-on-surface": "#f3f0f2",
                        "surface-container-highest": "#e4e2e4",
                        "community-teal": "#0D9488",
                        "on-primary-fixed": "#131b2e",
                        "on-secondary": "#ffffff",
                        "surface-container-low": "#f6f3f5",
                        "surface": "#fcf8fa",
                        "secondary-fixed": "#89f5e7",
                        "on-primary": "#ffffff",
                        "on-surface": "#1b1b1d",
                        "primary-fixed-dim": "#bec6e0",
                        "on-error": "#ffffff",
                        "surface-bright": "#fcf8fa",
                        "on-secondary-container": "#006f66",
                        "surface-tint": "#565e74",
                        "tertiary-fixed-dim": "#dec29a",
                        "on-tertiary-fixed": "#271901",
                        "on-surface-variant": "#45464d",
                        "secondary-container": "#86f2e4",
                        "reaction-red": "#EF4444",
                        "background": "#fcf8fa",
                        "primary-fixed": "#dae2fd"
                    },
                    spacing: {
                        "margin-mobile": "16px",
                        "article-width": "720px",
                        "margin-desktop": "64px",
                        "gutter": "24px",
                        "base": "4px",
                        "container-max": "1200px"
                    },
                    fontFamily: {
                        "article-body": ["Literata"],
                        "ui-medium": ["Inter"],
                        "headline-lg": ["Hanken Grotesk"],
                        "display-xl": ["Hanken Grotesk"],
                        "ui-small": ["Inter"],
                        "label-caps": ["Inter"],
                        "headline-lg-mobile": ["Hanken Grotesk"],
                        "display-xl-mobile": ["Hanken Grotesk"]
                    },
                    fontSize: {
                        "article-body": ["18px", {"lineHeight": "32px", "fontWeight": "400"}],
                        "ui-medium": ["16px", {"lineHeight": "24px", "fontWeight": "500"}],
                        "headline-lg": ["32px", {"lineHeight": "40px", "fontWeight": "700"}],
                        "display-xl": ["48px", {"lineHeight": "56px", "letterSpacing": "-0.02em", "fontWeight": "800"}],
                        "ui-small": ["14px", {"lineHeight": "20px", "fontWeight": "400"}],
                        "label-caps": ["12px", {"lineHeight": "16px", "letterSpacing": "0.05em", "fontWeight": "700"}],
                        "headline-lg-mobile": ["24px", {"lineHeight": "32px", "fontWeight": "700"}],
                        "display-xl-mobile": ["36px", {"lineHeight": "42px", "letterSpacing": "-0.02em", "fontWeight": "800"}]
                    }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hanken+Grotesk:wght@700;800&family=Inter:wght@400;500;700&family=Literata:opsz,wght@7..72,400&display=swap" rel="stylesheet">
    <script>
        document.documentElement.classList.remove('dark');
    </script>
    <style>
        body { font-family: Inter, sans-serif; background: #F8FAFC; color: #0F172A; }
        .font-display { font-family: "Hanken Grotesk", sans-serif; }
        .font-article { font-family: Literata, serif; }
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
    </style>
</head>
<body class="bg-paper-white text-official-ink antialiased h-[100dvh] overflow-hidden flex flex-col font-ui-medium">
    <!-- TopNavBar -->
    <header class="flex-shrink-0 bg-paper-white dark:bg-official-ink text-official-ink dark:text-paper-white font-ui-medium text-ui-medium border-b border-surface-border dark:border-on-primary-fixed-variant z-50 transition-colors duration-300">
        <div class="flex justify-between items-center w-full px-margin-mobile md:px-margin-desktop max-w-container-max mx-auto h-20">
            <!-- Brand -->
            <div class="flex items-center gap-gutter">
                <a class="font-display-xl-mobile md:font-display-xl text-display-xl-mobile md:text-display-xl font-extrabold text-official-ink dark:text-paper-white tracking-tight" href="{{ route('blog.index') }}">
                    Thesaurus
                </a>
            </div>

            <!-- Navigation Links (Desktop) -->
            <nav class="hidden md:flex gap-8 items-center font-ui-medium text-ui-medium">
                <a class="{{ request()->routeIs('blog.*') && !request()->routeIs('blog.create') && !request()->routeIs('blog.edit')
                    ? 'text-community-indigo dark:text-secondary-fixed font-bold border-b-2 border-community-indigo dark:border-secondary-fixed pb-1'
                    : 'text-on-surface-variant dark:text-surface-variant pb-1 hover:text-community-teal dark:hover:text-secondary-fixed-dim transition-colors' }}"
                   href="{{ route('blog.index') }}">
                    Official Blog
                </a>
                <a class="{{ request()->routeIs('community.*')
                    ? 'text-community-indigo dark:text-secondary-fixed font-bold border-b-2 border-community-indigo dark:border-secondary-fixed pb-1'
                    : 'text-on-surface-variant dark:text-surface-variant pb-1 hover:text-community-teal dark:hover:text-secondary-fixed-dim transition-colors' }}"
                   href="{{ route('community.index') }}">
                    Community Space
                </a>
                @auth
                    @if(auth()->user()->is_admin)
                        <a class="{{ request()->routeIs('admin.*')
                            ? 'text-community-indigo dark:text-secondary-fixed font-bold border-b-2 border-community-indigo dark:border-secondary-fixed pb-1'
                            : 'text-on-surface-variant dark:text-surface-variant pb-1 hover:text-community-teal dark:hover:text-secondary-fixed-dim transition-colors' }}"
                           href="{{ route('admin.dashboard') }}">
                            Admin
                        </a>
                    @endif
                @endauth
            </nav>

            <!-- Trailing Icons -->
            <div class="flex items-center gap-4 text-official-ink dark:text-paper-white">

                @auth
                    @if(auth()->user()->is_admin)
                        <a href="{{ route('admin.dashboard') }}" class="md:hidden text-on-surface-variant dark:text-surface-dim hover:text-community-teal dark:hover:text-secondary-fixed-dim transition-colors flex items-center justify-center p-2 rounded-full hover:bg-surface-container dark:hover:bg-primary-container transition-all" title="Admin Dashboard">
                            <span translate="no" class="material-symbols-outlined text-[24px]">admin_panel_settings</span>
                        </a>
                    @endif
                    <a href="{{ route('notifications.index') }}" class="hidden md:flex hover:text-community-teal dark:hover:text-secondary-fixed-dim transition-colors items-center justify-center relative p-2 rounded-full hover:bg-surface-container dark:hover:bg-primary-container transition-all">
                        <span translate="no" class="material-symbols-outlined text-[24px]">notifications</span>
                        @php($notifCount = auth()->user()->notifications()->whereNull('read_at')->count())
                        @if($notifCount > 0)
                            <span class="absolute top-0 right-0 w-5 h-5 bg-reaction-red text-white rounded-full text-[10px] flex items-center justify-center font-bold">{{ $notifCount > 9 ? '9+' : $notifCount }}</span>
                        @endif
                    </a>
                    <a href="{{ route('profile.show', auth()->user()->username) }}" class="hidden md:flex hover:text-community-teal dark:hover:text-secondary-fixed-dim transition-colors items-center justify-center p-2 rounded-full hover:bg-surface-container dark:hover:bg-primary-container transition-all">
                        <span translate="no" class="material-symbols-outlined text-[24px]">person</span>
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="flex items-center">
                        @csrf
                        <button class="text-on-surface-variant dark:text-surface-dim hover:text-community-teal dark:hover:text-secondary-fixed-dim transition-colors flex items-center justify-center p-2 rounded-full hover:bg-surface-container dark:hover:bg-primary-container transition-all">
                            <span translate="no" class="material-symbols-outlined text-[24px] md:hidden">logout</span>
                            <span class="hidden md:inline font-ui-small text-ui-small">Déconnexion</span>
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="font-ui-small text-ui-small text-on-surface-variant dark:text-surface-dim hover:text-community-teal dark:hover:text-secondary-fixed-dim transition-colors">Connexion</a>
                    <a href="{{ route('register') }}" class="font-ui-small text-ui-small bg-official-ink dark:bg-paper-white text-paper-white dark:text-official-ink px-4 py-2 rounded-DEFAULT hover:opacity-90 transition-opacity">Inscription</a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Scrollable Content Area -->
    <div class="flex-grow overflow-y-auto flex flex-col">
        <main class="flex-grow w-full max-w-container-max mx-auto px-margin-mobile md:px-margin-desktop py-10 pb-10">
        @if(session('success'))
            <div class="mb-6 rounded-DEFAULT border border-community-teal/30 bg-community-teal/5 px-4 py-3 font-ui-small text-ui-small text-community-teal flex items-center gap-2">
                <span translate="no" class="material-symbols-outlined text-sm">check_circle</span>
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-6 rounded-DEFAULT border border-reaction-red/30 bg-error-container px-4 py-3 font-ui-small text-ui-small text-on-error-container flex items-center gap-2">
                <span translate="no" class="material-symbols-outlined text-sm">error</span>
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
        {{ $slot ?? '' }}
    </main>

    <!-- Footer -->
    <footer class="bg-surface-container dark:bg-primary-container text-on-surface-variant dark:text-on-primary-container font-ui-small text-ui-small w-full mt-auto">
        <div class="w-full py-12 px-margin-mobile md:px-margin-desktop flex flex-col md:flex-row justify-between items-center gap-gutter max-w-container-max mx-auto">
            <div class="flex flex-col items-center md:items-start gap-4">
                <span class="font-headline-lg-mobile md:font-headline-lg text-headline-lg-mobile md:text-headline-lg font-bold text-official-ink dark:text-paper-white">
                    Thesaurus
                </span>
                <p class="font-ui-small text-ui-small text-on-surface-variant dark:text-on-primary-container">
                    © {{ date('Y') }} Thesaurus Modernist. All rights reserved.
                </p>
            </div>
            <nav class="flex flex-wrap justify-center gap-6 font-ui-small text-ui-small">
                <a class="text-on-surface-variant dark:text-on-primary-container hover:text-community-indigo dark:hover:text-secondary-fixed transition-colors" href="#">About</a>
                <a class="text-on-surface-variant dark:text-on-primary-container hover:text-community-indigo dark:hover:text-secondary-fixed transition-colors" href="#">Categories</a>
                <a class="text-on-surface-variant dark:text-on-primary-container hover:text-community-indigo dark:hover:text-secondary-fixed transition-colors" href="#">Privacy Policy</a>
                <a class="text-on-surface-variant dark:text-on-primary-container hover:text-community-indigo dark:hover:text-secondary-fixed transition-colors" href="#">Contact</a>
            </nav>
        </div>
    </footer>
    </div>

    <!-- Mobile Bottom Navigation Bar -->
    <nav class="md:hidden flex-shrink-0 w-full bg-paper-white dark:bg-official-ink border-t border-surface-border dark:border-surface-tint z-50 flex justify-around items-center pt-2 pb-[max(0.5rem,env(safe-area-inset-bottom))] px-2 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)]">
        <a href="{{ route('blog.index') }}" class="flex flex-col items-center justify-center w-full text-on-surface-variant dark:text-surface-dim {{ request()->routeIs('blog.*') && !request()->routeIs('blog.create') && !request()->routeIs('blog.edit') ? 'text-community-indigo dark:text-secondary-fixed' : '' }}">
            <span translate="no" class="material-symbols-outlined text-[24px]" style="{{ request()->routeIs('blog.*') && !request()->routeIs('blog.create') && !request()->routeIs('blog.edit') ? 'font-variation-settings: \'FILL\' 1;' : '' }}">article</span>
            <span class="font-ui-small text-[10px] mt-1">Blog</span>
        </a>
        <a href="{{ route('community.index') }}" class="flex flex-col items-center justify-center w-full text-on-surface-variant dark:text-surface-dim {{ request()->routeIs('community.*') ? 'text-community-indigo dark:text-secondary-fixed' : '' }}">
            <span translate="no" class="material-symbols-outlined text-[24px]" style="{{ request()->routeIs('community.*') ? 'font-variation-settings: \'FILL\' 1;' : '' }}">forum</span>
            <span class="font-ui-small text-[10px] mt-1">Forum</span>
        </a>
        @auth
        <a href="{{ route('notifications.index') }}" class="flex flex-col items-center justify-center w-full text-on-surface-variant dark:text-surface-dim relative {{ request()->routeIs('notifications.*') ? 'text-community-indigo dark:text-secondary-fixed' : '' }}">
            <div class="relative">
                <span translate="no" class="material-symbols-outlined text-[24px]" style="{{ request()->routeIs('notifications.*') ? 'font-variation-settings: \'FILL\' 1;' : '' }}">notifications</span>
                @php($notifCount = auth()->user()->notifications()->whereNull('read_at')->count())
                @if($notifCount > 0)
                    <span class="absolute -top-1 -right-1 w-4 h-4 bg-reaction-red text-white rounded-full text-[9px] flex items-center justify-center font-bold">{{ $notifCount > 9 ? '9+' : $notifCount }}</span>
                @endif
            </div>
            <span class="font-ui-small text-[10px] mt-1">Notifs</span>
        </a>
        <a href="{{ route('profile.show', auth()->user()->username) }}" class="flex flex-col items-center justify-center w-full text-on-surface-variant dark:text-surface-dim {{ request()->routeIs('profile.*') ? 'text-community-indigo dark:text-secondary-fixed' : '' }}">
            <span translate="no" class="material-symbols-outlined text-[24px]" style="{{ request()->routeIs('profile.*') ? 'font-variation-settings: \'FILL\' 1;' : '' }}">person</span>
            <span class="font-ui-small text-[10px] mt-1">Profil</span>
        </a>
        @else
        <a href="{{ route('login') }}" class="flex flex-col items-center justify-center w-full text-on-surface-variant dark:text-surface-dim">
            <span translate="no" class="material-symbols-outlined text-[24px]">login</span>
            <span class="font-ui-small text-[10px] mt-1">Connexion</span>
        </a>
        @endauth
    </nav>

</body>
</html>
