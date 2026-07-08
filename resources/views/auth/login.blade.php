<x-guest-layout>
    <div class="mb-6">
        <h1 class="font-display text-xl font-semibold text-ink">Selamat datang kembali</h1>
        <p class="text-sm text-ink/50 mt-1">Masuk untuk kelola data tambak Anda.</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded bg-paper border-lumpur/40 text-teal-mid shadow-sm focus:ring-teal-mid" name="remember">
                <span class="ms-2 text-sm text-ink/60">{{ __('Remember me') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm text-teal-mid hover:text-teal-deep font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-mid" href="{{ route('password.request') }}">
                    {{ __('Lupa password?') }}
                </a>
            @endif
        </div>

        <x-primary-button class="w-full justify-center py-3">
            {{ __('Masuk') }}
        </x-primary-button>
    </form>
</x-guest-layout>
