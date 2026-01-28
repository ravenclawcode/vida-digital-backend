<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required
                autofocus autocomplete="username" placeholder="Masukan email" /> <x-input-error
                :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" :value="__('Kata Sandi')" /> <x-text-input id="password"
                class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password"
                placeholder="Masukan kata sandi" /> <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    href="{{ route('password.request') }}">
                    {{ __('Lupa kata sandi Anda?') }}
                </a>
            @endif

            <x-primary-button class="ms-3 bg-[#D83A64] hover:bg-pink-700"> {{ __('Masuk') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>