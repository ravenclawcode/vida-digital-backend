<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Konselor') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-900">Tambah Akun Konselor Baru</h3>
                    <p class="text-sm text-gray-600">Akun ini akan digunakan oleh tenaga medis untuk login di aplikasi
                        mobile.</p>
                </div>

                @if(session('success'))
                    <div class="mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded shadow-sm">
                        {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('counselors.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <x-input-label for="username" value="Username" />
                            <x-text-input id="username" name="username" type="text" class="mt-1 block w-full"
                                :value="old('username')" required placeholder="Username Konselor" />
                            <x-input-error :messages="$errors->get('username')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="email" value="Email" />
                            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full"
                                :value="old('email')" required placeholder="email@rsdunda.com" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="password" value="Password" />
                            <x-text-input id="password" name="password" type="password" class="mt-1 block w-full"
                                required placeholder="********" />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <x-primary-button>
                            {{ __('Simpan Konselor') }}
                        </x-primary-button>
                    </div>
                </form>

                <hr class="my-8 border-gray-200">

                <div class="overflow-x-auto">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Daftar Konselor Aktif</h3>
                    <table class="w-full text-left border-collapse border border-gray-100">
                        <thead>
                            <tr class="bg-gray-50 text-gray-700 uppercase text-xs">
                                <th class="p-4 border-b">Username</th>
                                <th class="p-4 border-b">Email</th>
                                <th class="p-4 border-b text-center">Status</th>
                                <th class="p-4 border-b text-right">Dibuat Pada</th>
                                <th class="p-4 border-b text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($counselors as $counselor)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="p-4 text-sm font-medium text-gray-900">{{ $counselor->username }}</td>
                                    <td class="p-4 text-sm text-gray-600">{{ $counselor->email }}</td>
                                    <td class="p-4 text-center">
                                        <span
                                            class="px-2 py-1 text-xs font-semibold text-green-700 bg-green-100 rounded-full">Aktif</span>
                                    </td>
                                    <td class="p-4 text-sm text-gray-500 text-right">
                                        {{ $counselor->created_at->format('d M Y') }}</td>
                                    <td class="p-4 text-center">
                                        <form action="{{ route('counselors.destroy', $counselor->id) }}" method="POST"
                                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun konselor ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700 transition">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-8 text-center text-gray-400 italic">Belum ada data konselor
                                        terdaftar.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>