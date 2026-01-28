<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600 leading-relaxed">
        {{ __('Lupa kata sandi? Tidak masalah. Beritahu kami alamat email Anda dan kami akan mengirimkan tautan reset kata sandi yang memungkinkan Anda untuk membuat kata sandi baru.') }}
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required
                autofocus placeholder="Masukan email" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button class="bg-[#D83A64] hover:bg-pink-700">
                {{ __('Kirim Tautan Reset Kata Sandi') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>