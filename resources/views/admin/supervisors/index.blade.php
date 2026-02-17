<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="translate-y-5 opacity-0"
                x-transition:enter-end="translate-y-0 opacity-100" x-transition:leave="transition ease-in duration-300"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="fixed bottom-8 right-8 z-50 px-6 py-3 bg-white text-gray-700 text-sm rounded-lg shadow-xl border-l-4 border-green-500 font-medium ring-1 ring-black ring-opacity-5">
                {{ session('success') }}
            </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">Daftar Supervisor Aktif</h3>
                        <p class="text-sm text-gray-500">Kelola dan monitor akun konselor yang terdaftar.</p>
                    </div>

                    <x-primary-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'tambah-supervisor')"
                        class="bg-[#D83A64] hover:bg-pink-700 uppercase">
                        {{ __('Tambah Supervisor') }}
                    </x-primary-button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse border border-gray-100">
                        <thead>
                            <tr class="bg-gray-50 text-gray-700 uppercase text-xs">
                                <th class="p-4 border-b">Nama Pengguna</th>
                                <th class="p-4 border-b">Email</th>
                                <th class="p-4 border-b text-right">Dibuat Pada</th>
                                <th class="p-4 border-b text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($supervisors as $supervisor)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="p-4 text-sm font-medium text-gray-900">{{ $supervisor->username }}</td>
                                <td class="p-4 text-sm text-gray-600">{{ $supervisor->email }}</td>
                                <td class="p-4 text-sm text-gray-500 text-right">
                                    {{ $supervisor->created_at->format('d M Y') }}
                                </td>
                                <td class="p-4 text-center">
                                    <form action="{{ route('supervisor-management.destroy', $supervisor->id) }}" method="POST"
                                        onsubmit="return confirm('Hapus akun supervisor {{ $supervisor->username }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700 transition">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="py-24 text-center text-gray-400 italic text-sm">Belum ada data supervisor terdaftar.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <x-modal name="tambah-supervisor" :show="$errors->any()" focusable>
        <form action="{{ route('supervisor-management.store') }}" method="POST" class="p-6">
            @csrf
            <h2 class="text-lg font-medium text-gray-900">Tambah Akun Supervisor Baru</h2>
            <div class="mt-8 space-y-5">
                <div>
                    <x-input-label for="username" value="Nama Pengguna" />
                    <x-text-input id="username" name="username" type="text" class="mt-2 block w-full text-sm" required placeholder="Masukan nama pengguna" />
                </div>
                <div>
                    <x-input-label for="email" value="Email" />
                    <x-text-input id="email" name="email" type="email" class="mt-2 block w-full text-sm" required placeholder="Masukan email" />
                </div>
                <div>
                    <x-input-label for="password" value="Kata Sandi" />
                    <x-text-input id="password" name="password" type="password" class="mt-2 block w-full text-sm" required placeholder="Masukan kata sandi" />
                </div>
            </div>
            <div class="mt-8 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">Batal</x-secondary-button>
                <x-primary-button class="ms-3 bg-[#D83A64] hover:bg-pink-700 uppercase">Simpan</x-primary-button>
            </div>
        </form>
    </x-modal>
</x-app-layout>