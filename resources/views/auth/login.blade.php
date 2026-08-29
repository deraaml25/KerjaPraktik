<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <!-- Username -->
        <div>
            <label for="username" class="block text-sm font-medium text-ink mb-1.5">Username</label>
            <input
                id="username"
                type="text"
                name="username"
                value="{{ old('username') }}"
                required
                autofocus
                autocomplete="username"
                placeholder="Masukkan username Anda"
                class="w-full px-5 py-3.5 rounded-2xl border border-border bg-white/60 backdrop-blur-sm text-ink placeholder-muted focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all shadow-sm"
            />
            @error('username')
                <p class="mt-1.5 text-sm text-danger font-medium">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-sm font-semibold text-ink mb-1.5 ml-1">Password</label>
            <div class="relative" x-data="{ show: false }">
                <input
                    id="password"
                    :type="show ? 'text' : 'password'"
                    name="password"
                    required
                    autocomplete="current-password"
                    placeholder="Masukkan password Anda"
                    class="w-full px-5 py-3.5 rounded-2xl border border-border bg-white/60 backdrop-blur-sm text-ink placeholder-muted focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all shadow-sm pr-12"
                />
                <button type="button" @click="show = !show" class="absolute right-4 top-1/2 -translate-y-1/2 text-muted hover:text-ink transition-colors p-1">
                    <svg x-show="!show" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    <svg x-show="show" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                </button>
            </div>
            @error('password')
                <p class="mt-1.5 text-sm text-danger font-medium">{{ $message }}</p>
            @enderror
        </div>

        <!-- Remember Me -->
        <div class="flex items-center ml-1">
            <input id="remember_me" type="checkbox" name="remember" class="w-4 h-4 text-primary border-border rounded focus:ring-primary transition-colors cursor-pointer">
            <label for="remember_me" class="ml-2 text-sm text-muted cursor-pointer hover:text-ink transition-colors">Ingat saya di perangkat ini</label>
        </div>

        <!-- Submit -->
        <button
            id="btn-masuk"
            type="submit"
            class="w-full mt-2 bg-primary hover:bg-primary-light text-ink font-bold tracking-wide py-4 px-6 rounded-2xl transition-all duration-300 shadow-[0_4px_14px_0_rgb(149,187,234,0.39)] hover:shadow-[0_6px_20px_rgba(149,187,234,0.23)] hover:-translate-y-0.5 active:scale-[0.98] focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 focus:ring-offset-background"
        >
            Masuk ke SiPerangkat
        </button>
    </form>
</x-guest-layout>
