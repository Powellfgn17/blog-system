<!DOCTYPE html>
<html class="light" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Register | Thesaurus</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Hanken+Grotesk:wght@700;800&family=Inter:wght@400;500;700&family=Literata:ital,wght@0,400;1,400&family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Hanken+Grotesk:wght@100..900&family=Inter:wght@100..900&display=swap" rel="stylesheet"/>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        .form-input-focus:focus {
            outline: none;
            border-color: #0D9488;
            box-shadow: 0 0 0 1px #0D9488;
        }
        .split-layout-image {
            background-size: cover;
            background-position: center;
        }
    </style>
</head>
<body class="bg-paper-white text-on-surface min-h-screen flex flex-col font-ui-medium text-ui-medium">
    <main class="flex-grow flex flex-col md:flex-row">
        <!-- Left Section: Aesthetic/Benefit Branding -->
        <div class="hidden md:flex md:w-5/12 lg:w-1/2 relative bg-official-ink p-margin-desktop flex-col justify-between overflow-hidden">
            <!-- Decorative Background Element -->
            <div class="absolute inset-0 opacity-20 pointer-events-none split-layout-image" style="background-image: url('https://lh3.googleusercontent.com/aida/AP1WRLudr7O6cdFuQJaoCl_0eqUrTgNz9PBTN7KYSVh8QvlJYZBidJcHqe8uxtSk-h0UaT2-do15FGkgQunVIcDi4CwiMJmjh-w7-TAeLqMiyzF5odNagfeHTjQw6N1lhdNiFP5S08CtrivTGJqukoVzrh6iW_zMSxzhL2KFfKqYC-1G1Uo_e5NLUHG3FeO_wJfW6SxcKsV6rw_nTgKseKWeBwX0zt5lq39npa3581HHKAA8sboKhW4BtcvLN8YZ')">
            </div>
            
            <div class="relative z-10">
                <h1 class="font-display-xl text-display-xl text-paper-white mb-8">Thesaurus</h1>
                <div class="space-y-gutter max-w-md">
                    <div class="flex items-start gap-4">
                        <span class="material-symbols-outlined text-community-teal text-3xl">edit_note</span>
                        <div>
                            <h3 class="font-headline-lg text-headline-lg text-paper-white leading-tight">Curate Your Thoughts</h3>
                            <p class="text-on-primary-container font-article-body text-article-body mt-2">Publish long-form editorial content in a distraction-free, premium reading environment.</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start gap-4">
                        <span class="material-symbols-outlined text-community-indigo text-3xl">groups</span>
                        <div>
                            <h3 class="font-headline-lg text-headline-lg text-paper-white leading-tight">Engage the Community</h3>
                            <p class="text-on-primary-container font-article-body text-article-body mt-2">Join vibrant discussions, react to trending ideas, and build a network of experts.</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start gap-4">
                        <span class="material-symbols-outlined text-secondary-fixed text-3xl">auto_awesome</span>
                        <div>
                            <h3 class="font-headline-lg text-headline-lg text-paper-white leading-tight">AI-Enhanced Insights</h3>
                            <p class="text-on-primary-container font-article-body text-article-body mt-2">Leverage smart summaries and semantic analysis to navigate complex community threads.</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="relative z-10">
                <p class="text-on-primary-container text-ui-small">Join 25,000+ modernists defining the future of digital community.</p>
            </div>
        </div>

        <!-- Right Section: Registration Form -->
        <div class="flex-grow flex items-center justify-center p-margin-mobile md:p-margin-desktop bg-paper-white">
            <div class="w-full max-w-md">
                <header class="mb-10 md:hidden">
                    <h1 class="font-display-xl-mobile text-display-xl-mobile text-official-ink">Thesaurus</h1>
                </header>
                
                <div class="mb-8">
                    <h2 class="font-headline-lg text-headline-lg text-official-ink mb-2">Create Account</h2>
                    <p class="text-on-surface-variant font-ui-medium">Enter your details to start your journey.</p>
                </div>
                
                <form class="space-y-6" action="{{ route('register') }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label for="username" class="font-label-caps text-label-caps text-on-surface-variant uppercase">Username</label>
                            <input type="text" id="username" name="username" value="{{ old('username') }}" required minlength="3" maxlength="30" pattern="^[a-zA-Z0-9_]+$" title="Lettres, chiffres et underscores uniquement" autofocus autocomplete="username" class="w-full px-4 py-3 border border-surface-border rounded bg-surface-container-lowest text-on-surface form-input-focus transition-all font-ui-medium" placeholder="johndoe">
                            @error('username')<p class="text-error text-ui-small">{{ $message }}</p>@enderror
                        </div>
                        <div class="space-y-2">
                            <label for="name" class="font-label-caps text-label-caps text-on-surface-variant uppercase">Display Name</label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}" required autocomplete="name" class="w-full px-4 py-3 border border-surface-border rounded bg-surface-container-lowest text-on-surface form-input-focus transition-all font-ui-medium" placeholder="John Doe">
                            @error('name')<p class="text-error text-ui-small">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label for="email" class="font-label-caps text-label-caps text-on-surface-variant uppercase">Email Address</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required autocomplete="email" class="w-full px-4 py-3 border border-surface-border rounded bg-surface-container-lowest text-on-surface form-input-focus transition-all font-ui-medium" placeholder="john@example.com">
                        @error('email')<p class="text-error text-ui-small">{{ $message }}</p>@enderror
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label for="password" class="font-label-caps text-label-caps text-on-surface-variant uppercase">Password</label>
                            <input type="password" id="password" name="password" required minlength="8" autocomplete="new-password" class="w-full px-4 py-3 border border-surface-border rounded bg-surface-container-lowest text-on-surface form-input-focus transition-all font-ui-medium" placeholder="••••••••">
                            @error('password')<p class="text-error text-ui-small">{{ $message }}</p>@enderror
                        </div>
                        <div class="space-y-2">
                            <label for="password_confirmation" class="font-label-caps text-label-caps text-on-surface-variant uppercase">Confirm</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" required minlength="8" autocomplete="new-password" class="w-full px-4 py-3 border border-surface-border rounded bg-surface-container-lowest text-on-surface form-input-focus transition-all font-ui-medium" placeholder="••••••••">
                            @error('password_confirmation')<p class="text-error text-ui-small">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    
                    <div class="flex items-start gap-3 py-2">
                        <input type="checkbox" id="terms" name="terms" required class="mt-1 h-4 w-4 rounded border-surface-border text-community-teal focus:ring-community-teal cursor-pointer">
                        <label for="terms" class="text-ui-small text-on-surface-variant">
                            I agree to the <a href="#" class="text-official-ink font-bold hover:underline">Terms & Conditions</a> and <a href="#" class="text-official-ink font-bold hover:underline">Privacy Policy</a>.
                        </label>
                    </div>
                    
                    <button type="submit" class="w-full py-4 px-6 bg-official-ink text-paper-white font-bold rounded hover:opacity-90 active:scale-[0.98] transition-all flex justify-center items-center gap-2 group">
                        Create Account
                        <span class="material-symbols-outlined group-hover:translate-x-1 transition-transform">arrow_forward</span>
                    </button>
                </form>
                
                <div class="mt-12 text-center border-t border-surface-border pt-8">
                    <p class="text-on-surface-variant">
                        Already have an account? 
                        <a href="{{ route('login') }}" class="text-community-indigo font-bold hover:underline transition-colors ml-1">Log in here</a>
                    </p>
                </div>
                
                <!-- Subtle Community Feed Snippet for Engagement -->
                <div class="mt-12 p-6 bg-surface-container-low border border-surface-border rounded-xl hidden lg:block">
                    <div class="flex items-center gap-2 mb-4">
                        <span class="material-symbols-outlined text-community-teal" style="font-variation-settings: 'FILL' 1;">spark</span>
                        <span class="font-label-caps text-label-caps text-community-teal uppercase">Trending Discussion</span>
                    </div>
                    <p class="font-article-body text-ui-medium italic text-on-surface mb-3">"The intersection of minimalist design and social utility is the next frontier of digital identity..."</p>
                    
                    <div class="flex items-center justify-between">
                        <div class="flex -space-x-2">
                            <div class="w-8 h-8 rounded-full border-2 border-paper-white bg-surface-variant"></div>
                            <div class="w-8 h-8 rounded-full border-2 border-paper-white bg-secondary-fixed"></div>
                            <div class="w-8 h-8 rounded-full border-2 border-paper-white bg-primary-fixed-dim"></div>
                        </div>
                        <span class="text-ui-small text-on-surface-variant font-bold">142 members reacting</span>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
    <!-- Simplified Footer for Auth Flow -->
    <footer class="w-full py-8 px-margin-mobile md:px-margin-desktop bg-surface-container-low border-t border-surface-border">
        <div class="max-w-container-max mx-auto flex flex-col md:flex-row justify-between items-center gap-4">
            <span class="font-headline-lg text-official-ink">Thesaurus</span>
            <div class="flex gap-gutter text-ui-small text-on-surface-variant">
                <a href="#" class="hover:text-official-ink transition-colors">Help</a>
                <a href="#" class="hover:text-official-ink transition-colors">Privacy</a>
                <a href="#" class="hover:text-official-ink transition-colors">Contact</a>
            </div>
            <p class="text-ui-small text-on-surface-variant">© {{ date('Y') }} Thesaurus Modernist.</p>
        </div>
    </footer>
    
    <script>
        const inputs = document.querySelectorAll('input[type="text"], input[type="email"], input[type="password"]');
        inputs.forEach(input => {
            input.addEventListener('focus', () => {
                input.parentElement.querySelector('label').classList.replace('text-on-surface-variant', 'text-community-teal');
            });
            input.addEventListener('blur', () => {
                input.parentElement.querySelector('label').classList.replace('text-community-teal', 'text-on-surface-variant');
            });
        });

        window.addEventListener('mousemove', (e) => {
            const parallaxImg = document.querySelector('.split-layout-image');
            if (parallaxImg) {
                const x = (window.innerWidth - e.pageX) / 100;
                const y = (window.innerHeight - e.pageY) / 100;
                parallaxImg.style.transform = `scale(1.1) translate(${x}px, ${y}px)`;
            }
        });
    </script>
</body>
</html>
