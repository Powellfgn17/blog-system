@extends('layouts.app')

@section('content')
<section class="max-w-3xl mx-auto px-0 md:px-margin-desktop py-4 flex flex-col gap-8">
    <div>
        <h1 class="font-display-xl-mobile md:font-display-xl font-extrabold text-official-ink dark:text-paper-white tracking-tight">Paramètres du profil</h1>
        <p class="font-ui-medium text-ui-medium text-on-surface-variant dark:text-surface-dim mt-2">Gérez vos informations personnelles et vos paramètres de sécurité.</p>
    </div>

    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="bg-paper-white dark:bg-official-ink border border-surface-border dark:border-surface-tint rounded-2xl shadow-[0px_4px_20px_rgba(15,23,42,0.02)] overflow-hidden">
        @csrf
        @method('PUT')
        
        <div class="p-8 md:p-10 flex flex-col gap-8">
            <!-- Profil Public -->
            <div class="flex flex-col gap-6">
                <div class="flex items-center gap-2 border-b border-surface-border dark:border-surface-tint pb-4">
                    <span translate="no" class="material-symbols-outlined text-community-indigo dark:text-secondary-fixed">person</span>
                    <h2 class="font-headline-lg-mobile text-xl font-bold text-official-ink dark:text-paper-white">Profil Public</h2>
                </div>

                <div class="flex flex-col md:flex-row gap-8 items-start">
                    <!-- Avatar Preview & Upload -->
                    <div class="flex flex-col items-center gap-4">
                        <div class="relative group">
                            <div class="w-32 h-32 rounded-full overflow-hidden border-2 border-surface-border dark:border-surface-tint group-hover:border-community-indigo dark:group-hover:border-secondary-fixed transition-colors">
                                <img id="avatar-preview" src="{{ $user->avatar_url }}" alt="Avatar actuel" class="w-full h-full object-cover">
                            </div>
                            <label for="avatar" class="absolute inset-0 flex items-center justify-center bg-black/50 text-white rounded-full opacity-0 group-hover:opacity-100 cursor-pointer transition-opacity">
                                <span translate="no" class="material-symbols-outlined">photo_camera</span>
                            </label>
                        </div>
                        <div class="text-center">
                            <label for="avatar" class="font-ui-small text-ui-small font-semibold text-community-indigo dark:text-secondary-fixed cursor-pointer hover:underline">Changer la photo</label>
                            <input type="file" id="avatar" name="avatar" class="hidden" accept="image/*" onchange="previewAvatar(this)">
                            @error('avatar') <p class="text-reaction-red text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Fields -->
                    <div class="flex-grow w-full flex flex-col gap-6">
                        <div class="flex flex-col gap-1">
                            <label class="font-ui-small text-ui-small font-semibold text-official-ink dark:text-paper-white">Nom affiché</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full bg-surface-container-low dark:bg-primary-container border border-surface-border dark:border-surface-tint text-official-ink dark:text-paper-white rounded-lg focus:border-community-teal focus:ring-1 focus:ring-community-teal font-ui-medium text-ui-medium py-3 px-4 transition-colors">
                            @error('name') <span class="text-reaction-red text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="flex flex-col gap-1">
                            <label class="font-ui-small text-ui-small font-semibold text-official-ink dark:text-paper-white">Biographie</label>
                            <textarea name="bio" rows="4" class="w-full bg-surface-container-low dark:bg-primary-container border border-surface-border dark:border-surface-tint text-official-ink dark:text-paper-white rounded-lg focus:border-community-teal focus:ring-1 focus:ring-community-teal font-article-body text-base py-3 px-4 resize-none transition-colors" placeholder="Parlez-nous un peu de vous...">{{ old('bio', $user->bio) }}</textarea>
                            <p class="text-on-surface-variant dark:text-surface-dim text-xs mt-1">Apparaît sur votre page de profil publique.</p>
                            @error('bio') <span class="text-reaction-red text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sécurité -->
            <div class="flex flex-col gap-6 pt-6 mt-2">
                <div class="flex items-center gap-2 border-b border-surface-border dark:border-surface-tint pb-4">
                    <span translate="no" class="material-symbols-outlined text-community-indigo dark:text-secondary-fixed">lock</span>
                    <h2 class="font-headline-lg-mobile text-xl font-bold text-official-ink dark:text-paper-white">Sécurité</h2>
                </div>

                <div class="grid md:grid-cols-2 gap-6">
                    <div class="flex flex-col gap-1">
                        <label class="font-ui-small text-ui-small font-semibold text-official-ink dark:text-paper-white">Nouveau mot de passe</label>
                        <input type="password" name="password" class="w-full bg-surface-container-low dark:bg-primary-container border border-surface-border dark:border-surface-tint text-official-ink dark:text-paper-white rounded-lg focus:border-community-teal focus:ring-1 focus:ring-community-teal font-ui-medium text-ui-medium py-3 px-4 transition-colors" placeholder="••••••••">
                        @error('password') <span class="text-reaction-red text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="font-ui-small text-ui-small font-semibold text-official-ink dark:text-paper-white">Confirmer le mot de passe</label>
                        <input type="password" name="password_confirmation" class="w-full bg-surface-container-low dark:bg-primary-container border border-surface-border dark:border-surface-tint text-official-ink dark:text-paper-white rounded-lg focus:border-community-teal focus:ring-1 focus:ring-community-teal font-ui-medium text-ui-medium py-3 px-4 transition-colors" placeholder="••••••••">
                    </div>
                </div>
                <p class="text-on-surface-variant dark:text-surface-dim text-xs">Laissez ces champs vides si vous ne souhaitez pas modifier votre mot de passe.</p>
            </div>
        </div>

        <div class="bg-surface-container-low dark:bg-primary-container/50 border-t border-surface-border dark:border-surface-tint p-6 flex items-center justify-between">
            <a href="{{ route('profile.show', $user->username) }}" class="font-ui-medium text-ui-medium text-on-surface-variant dark:text-surface-dim hover:text-official-ink dark:hover:text-paper-white transition-colors">Annuler</a>
            <button type="submit" class="bg-official-ink dark:bg-paper-white text-paper-white dark:text-official-ink px-8 py-3 rounded-full font-ui-medium text-ui-medium font-bold shadow-lg hover:opacity-90 active:scale-95 transition-all">
                Enregistrer les modifications
            </button>
        </div>
    </form>

    <!-- Zone de danger -->
    <div class="bg-error-container/30 dark:bg-reaction-red/10 border border-reaction-red/20 rounded-2xl p-8 mt-4 flex flex-col md:flex-row justify-between items-center gap-6">
        <div>
            <h3 class="font-headline-lg-mobile text-lg font-bold text-reaction-red mb-1">Supprimer le compte</h3>
            <p class="font-ui-small text-sm text-on-surface-variant dark:text-surface-dim">Cette action est irréversible. Toutes vos données, publications et commentaires seront supprimés définitivement.</p>
        </div>
        <form method="POST" action="{{ route('profile.destroy') }}" onsubmit="return confirm('Êtes-vous absolument sûr de vouloir supprimer votre compte ? Cette action est irréversible.');">
            @csrf
            @method('DELETE')
            <button type="submit" class="whitespace-nowrap px-6 py-2 border border-reaction-red text-reaction-red hover:bg-reaction-red hover:text-white rounded-full font-ui-medium text-ui-medium transition-colors">
                Supprimer mon compte
            </button>
        </form>
    </div>
</section>

<script>
    function previewAvatar(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('avatar-preview').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection
