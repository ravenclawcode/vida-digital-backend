<x-app-layout>
    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @if (session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="translate-y-5 opacity-0"
                x-transition:enter-end="translate-y-0 opacity-100" x-transition:leave="transition ease-in duration-300"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="fixed bottom-4 right-4 left-4 sm:left-auto sm:bottom-8 sm:right-8 z-50 px-6 py-3 bg-white text-gray-700 text-sm rounded-lg shadow-xl border-l-4 border-green-500 font-medium ring-1 ring-black ring-opacity-5">
                {{ session('success') }}
            </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm rounded-lg sm:rounded-lg p-4 sm:p-6">

                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">Token Registrasi</h3>
                        <p class="text-sm text-gray-500">Buat kode unik untuk pasien baru agar mereka dapat mendaftar.
                        </p>
                    </div>

                    <div class="flex items-center gap-3 w-full sm:w-auto">
                        <div class="flex items-center gap-2 w-full sm:w-auto">
                            <a href="{{ route('tokens.index') }}"
                                class="inline-flex items-center justify-center px-3 py-2 sm:px-2 sm:py-[7px] bg-white border border-gray-200 rounded-md shadow-sm hover:bg-gray-50 transition-all duration-200 shrink-0"
                                title="Refresh Halaman">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                            </a>

                            <form action="{{ route('tokens.store') }}" method="POST">
                                @csrf
                                <x-primary-button class="bg-[#D83A64] hover:bg-pink-700 uppercase w-full sm:w-auto justify-center">
                                    {{ __('Buat Token') }}
                                </x-primary-button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="hidden sm:block overflow-x-auto -mx-4 sm:mx-0 px-4 sm:px-0">
                    <table class="min-w-[640px] w-full text-left border-collapse border border-gray-100">
                        <thead>
                            <tr class="bg-gray-50 text-gray-700 uppercase text-xs">
                                <th class="p-4 border-b">Kode Token</th>
                                <th class="p-4 border-b text-center">Status</th>
                                <th class="p-4 border-b text-right">Dibuat Pada</th>
                                <th class="p-4 border-b text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($tokens as $token)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="p-4 text-sm font-medium font-mono text-gray-900 tracking-wider">
                                    {{ $token->token_code }}
                                </td>
                                <td class="p-4 text-center">
                                    @if($token->is_used)
                                    <span
                                        class="px-2 py-1 text-xs font-semibold text-red-700 bg-red-100 rounded-full">Terpakai</span>
                                    @else
                                    <span
                                        class="px-2 py-1 text-xs font-semibold text-green-700 bg-green-100 rounded-full">Tersedia</span>
                                    @endif
                                </td>
                                <td class="p-4 text-sm text-gray-500 text-right">
                                    {{ $token->created_at->format('d M Y') }}
                                </td>
                                <td class="p-4 text-center">
                                    @if(!$token->is_used)
                                    <form action="{{ route('tokens.destroy', $token->id) }}" method="POST"
                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus token ini?')">
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
                                    @else
                                    <span class="text-gray-300" title="Token yang sudah terpakai tidak bisa dihapus">
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
                                <td colspan="5" class="py-24 text-center">
                                    <div class="flex flex-col items-center justify-center w-full">
                                        <span class="text-gray-400 italic text-sm tracking-wide">
                                            Belum ada token yang dibuat.
                                        </span>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Card list khusus HP kecil (desktop tetap pakai tabel di atas) --}}
                <div class="sm:hidden space-y-3">
                    @forelse($tokens as $token)
                    <div class="border border-gray-100 rounded-xl p-4 bg-white">
                        <div class="flex justify-between items-start gap-3">
                            <div class="min-w-0">
                                <p class="font-mono text-sm font-bold text-gray-900 tracking-wider break-all">{{ $token->token_code }}</p>
                                <p class="text-xs text-gray-500 mt-1">Dibuat {{ $token->created_at->format('d M Y') }}</p>
                            </div>
                            @if($token->is_used)
                            <span class="shrink-0 px-2 py-1 text-[11px] font-semibold text-red-700 bg-red-100 rounded-full">Terpakai</span>
                            @else
                            <span class="shrink-0 px-2 py-1 text-[11px] font-semibold text-green-700 bg-green-100 rounded-full">Tersedia</span>
                            @endif
                        </div>
                        <div class="mt-3 flex gap-2">
                            <button type="button" onclick="navigator.clipboard.writeText('{{ $token->token_code }}')"
                                class="flex-1 inline-flex items-center justify-center px-3 py-2 text-xs font-semibold text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                                Salin Kode
                            </button>
                            @if(!$token->is_used)
                            <form action="{{ route('tokens.destroy', $token->id) }}" method="POST" class="flex-1"
                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus token ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="w-full inline-flex items-center justify-center px-3 py-2 text-xs font-semibold text-red-600 bg-red-50 rounded-lg hover:bg-red-100 transition">
                                    Hapus
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="py-16 text-center">
                        <span class="text-gray-400 italic text-sm tracking-wide">Belum ada token yang dibuat.</span>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>