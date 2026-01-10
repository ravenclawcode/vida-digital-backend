<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Generate Token Aktivasi') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-900">Kontrol Token Registrasi</h3>
                    <p class="text-sm text-gray-600">Buat kode unik untuk pasien baru agar mereka dapat mendaftar secara
                        anonim.</p>
                </div>

                @if(session('success'))
                    <div class="mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded shadow-sm">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded shadow-sm">
                        {{ session('error') }}
                    </div>
                @endif

                <form action="{{ route('tokens.store') }}" method="POST">
                    @csrf
                    <x-primary-button>
                        {{ __('Buat Token Baru') }}
                    </x-primary-button>
                </form>

                <div class="mt-8 overflow-x-auto">
                    <table class="w-full text-left border-collapse border border-gray-100">
                        <thead>
                            <tr class="bg-gray-50 text-gray-700 uppercase text-xs">
                                <th class="p-4 border-b">Kode Token</th>
                                <th class="p-4 border-b text-center">Status</th>
                                <th class="p-4 border-b">Tanggal Dibuat</th>
                                <th class="p-4 border-b text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($tokens as $token)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="p-4 border-b font-mono font-bold text-blue-600 tracking-wider">
                                        {{ $token->token_code }}
                                    </td>
                                    <td class="p-4 border-b text-center">
                                        @if($token->is_used)
                                            <span class="text-red-600 bg-red-100 px-3 py-1 rounded-full text-xs font-semibold">
                                                Terpakai
                                            </span>
                                        @else
                                            <span
                                                class="text-green-600 bg-green-100 px-3 py-1 rounded-full text-xs font-semibold">
                                                Tersedia
                                            </span>
                                        @endif
                                    </td>
                                    <td class="p-4 border-b text-sm text-gray-500">
                                        {{ $token->created_at->format('d M Y, H:i') }}
                                    </td>
                                    <td class="p-4 border-b text-center">
                                        @if(!$token->is_used)
                                            <form action="{{ route('tokens.destroy', $token->id) }}" method="POST"
                                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus token yang belum terpakai ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:text-red-700 transition"
                                                    title="Hapus Token">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-gray-400">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                                </svg>
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="p-8 text-center text-gray-400 italic">Belum ada token yang
                                        dibuat.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>