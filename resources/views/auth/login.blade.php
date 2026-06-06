<!DOCTYPE html>
<html class="light" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sign In - Thesaurus</title>
    <!-- Material Symbols -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Hanken+Grotesk:wght@700;800&family=Inter:wght@400;500;700&family=Literata:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet"/>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        html, body {
            overflow-x: hidden;
            min-width: 0;
        }
        body {
            background-color: #f0edef; /* surface-container */
        }
        header, footer, nav, main {
            max-width: 100%;
        }
        header *, footer * {
            max-width: 100%;
        }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            vertical-align: middle;
        }
        .login-card {
            box-shadow: 0px 4px 20px rgba(15, 23, 42, 0.05);
        }
        .input-focus-ring:focus {
            border-color: #0D9488; /* community-teal */
            outline: none;
            box-shadow: 0 0 0 2px rgba(13, 148, 136, 0.1);
        }
    </style>
</head>
<body class="min-h-screen flex flex-col font-ui-medium text-on-surface selection:bg-community-indigo selection:text-white">
    <!-- Transactional Header -->
    <header class="w-full px-6 md:px-margin-desktop h-20 flex items-center justify-between">
        <div class="font-display-xl text-display-xl-mobile md:text-display-xl font-extrabold text-official-ink">
            Thesaurus
        </div>
        <a class="flex items-center gap-2 text-on-surface-variant hover:text-official-ink transition-colors" href="/">
            <span translate="no" class="material-symbols-outlined" data-icon="arrow_back">arrow_back</span>
            <span class="font-ui-small text-ui-small">Back to home</span>
        </a>
    </header>
    <main class="flex-grow flex items-center justify-center px-4 py-12">
        <div class="login-card w-full max-w-[440px] bg-paper-white border border-surface-border p-8 md:p-12 rounded-lg">
            <!-- Title Section -->
            <div class="text-center mb-10">
                <h1 class="font-headline-lg-mobile md:font-headline-lg text-headline-lg-mobile md:text-headline-lg text-official-ink mb-2">Welcome Back</h1>
                <p class="font-ui-small text-ui-small text-on-surface-variant">Sign in to your Thesaurus account to continue.</p>
            </div>
            
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <!-- Login Form -->
            <form action="{{ route('login') }}" class="space-y-6" method="POST">
                @csrf
                <div>
                    <label class="block font-label-caps text-label-caps text-on-surface-variant uppercase mb-2 tracking-wider" for="email">Email Address</label>
                    <input class="w-full px-4 py-3 bg-white border border-surface-border rounded-sm font-ui-medium text-ui-medium text-official-ink input-focus-ring transition-all" id="email" name="email" value="{{ old('email') }}" placeholder="name@example.com" required autofocus autocomplete="username" type="text"/>
                    @error('email')
                        <p class="text-error text-ui-small mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <div class="flex justify-between mb-2">
                        <label class="block font-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider" for="password">Password</label>
                        @if (Route::has('password.request'))
                            <a class="font-ui-small text-ui-small text-community-indigo hover:text-community-teal transition-colors" href="{{ route('password.request') }}">Forgot Password?</a>
                        @endif
                    </div>
                    <div class="relative">
                        <input class="w-full px-4 py-3 bg-white border border-surface-border rounded-sm font-ui-medium text-ui-medium text-official-ink input-focus-ring transition-all" id="password" name="password" placeholder="••••••••" required autocomplete="current-password" type="password"/>
                        <button class="absolute right-3 top-1/2 -translate-y-1/2 text-on-surface-variant hover:text-official-ink" onclick="togglePassword()" type="button">
                            <span translate="no" class="material-symbols-outlined" data-icon="visibility" id="eye-icon">visibility</span>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-error text-ui-small mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="block mt-4">
                    <label for="remember_me" class="inline-flex items-center">
                        <input id="remember_me" type="checkbox" class="rounded border-surface-border text-community-teal shadow-sm focus:ring-community-teal" name="remember">
                        <span class="ms-2 text-ui-small text-on-surface-variant">{{ __('Remember me') }}</span>
                    </label>
                </div>

                <button class="w-full bg-official-ink text-white font-ui-medium text-ui-medium py-3 rounded-sm hover:opacity-90 active:scale-[0.98] transition-all" type="submit">
                    Sign In
                </button>
            </form>
            
            <!-- Divider -->
            <div class="relative my-8">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-surface-border"></div>
                </div>
                <div class="relative flex justify-center text-label-caps">
                    <span class="bg-paper-white px-4 text-on-surface-variant uppercase tracking-widest">Or continue with</span>
                </div>
            </div>
            
            <!-- Social Logins -->
            <div class="grid grid-cols-2 gap-4">
                <button class="flex items-center justify-center gap-2 px-4 py-3 border border-surface-border rounded-sm bg-white hover:bg-surface-container-low transition-colors font-ui-small text-ui-small text-official-ink">
                    <img alt="Google" class="w-5 h-5" src="https://lh3.googleusercontent.com/aida/AP1WRLv0jAmJBUQOvP8qSAIcjPDAFcXN5Zs9NyJFjkxAo2AzJUJjPVAuVHvaB6GFHBtw9ZrfBFNIOt3InrVHWYJr2t151Z-EiMW7vlKcoPRpqqfguunOFfBKFpLb71PDq73bSTDn5uwRhQ2q82yXHL6wdMna7AcOtt72W4xaZ-_P7ir62niV3oeqzmTrZG17r0FMD9EfOOkKrkCcpfvkHSya-lfMXQ4b45Fg1El06hRNkAyIdCx_sy4E1Gamm5w"/>
                    Google
                </button>
                <button class="flex items-center justify-center gap-2 px-4 py-3 border border-surface-border rounded-sm bg-white hover:bg-surface-container-low transition-colors font-ui-small text-ui-small text-official-ink">
                    <span translate="no" class="material-symbols-outlined text-official-ink" data-icon="terminal">terminal</span>
                    GitHub
                </button>
            </div>
            
            <!-- Footer Link -->
            <p class="mt-10 text-center font-ui-small text-ui-small text-on-surface-variant">
                Don't have an account? 
                <a class="font-bold text-official-ink hover:text-community-indigo transition-colors" href="{{ route('register') }}">Sign Up</a>
            </p>
        </div>
    </main>
    
    <!-- Footer Segment -->
    <footer class="w-full py-8 px-6 text-center">
        <p class="font-ui-small text-ui-small text-on-surface-variant opacity-60">
            © {{ date('Y') }} Thesaurus Modernist. Secure authentication powered by Nexus.
        </p>
    </footer>
    
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.innerText = 'visibility_off';
            } else {
                passwordInput.type = 'password';
                eyeIcon.innerText = 'visibility';
            }
        }

        document.querySelectorAll('button').forEach(button => {
            button.addEventListener('mousedown', () => button.classList.add('scale-95'));
            button.addEventListener('mouseup', () => button.classList.remove('scale-95'));
            button.addEventListener('mouseleave', () => button.classList.remove('scale-95'));
        });
    </script>
</body>
</html>
