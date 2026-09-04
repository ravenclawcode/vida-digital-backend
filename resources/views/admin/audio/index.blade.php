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
                        <h3 class="text-lg font-medium text-gray-900">Daftar Audio</h3>
                        <p class="text-sm text-gray-500">Kelola konten audio relaksasi dan meditasi untuk pengguna.</p>
                    </div>

                    <x-primary-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'tambah-audio')"
                        class="bg-[#D83A64] hover:bg-pink-700 uppercase w-full sm:w-auto justify-center">
                        {{ __('Tambah Audio') }}
                    </x-primary-button>
                </div>

                <div class="hidden sm:block overflow-x-auto -mx-4 sm:mx-0 px-4 sm:px-0">
                    <table class="min-w-[640px] w-full text-left border-collapse border border-gray-100">
                        <thead>
                            <tr class="bg-gray-50 text-gray-700 uppercase text-xs">
                                <th class="p-4 border-b">Cover</th>
                                <th class="p-4 border-b">Judul</th>
                                <th class="p-4 border-b text-center">Kategori</th>
                                <th class="p-4 border-b text-center">Durasi</th>
                                <th class="p-4 border-b text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($audios as $audio)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="p-4">
                                    @if($audio->cover_url)
                                    <img src="{{ $audio->cover_url }}"
                                        class="w-12 h-12 object-cover rounded-lg shadow-sm" alt="Cover">
                                    @else
                                    <div
                                        class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center text-gray-400 text-[10px]">
                                        No Cover</div>
                                    @endif
                                </td>

                                <td class="p-4 text-sm font-medium text-gray-900">
                                    {{ $audio->title }}
                                </td>

                                <td class="p-4 text-center">
                                    <span style="color: #E43371; background-color: #FFE5F0;"
                                        class="px-3 py-1 text-xs font-bold rounded-full tracking-tight">
                                        {{ $audio->category }}
                                    </span>
                                </td>
                                <td class="p-4 text-center text-sm text-gray-600 font-medium">
                                    {{ $audio->duration }}
                                </td>
                                <td class="p-4 text-center">
                                    <form action="{{ route('audio.destroy', $audio->id) }}" method="POST"
                                        onsubmit="return confirm('Hapus audio ini?')">
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
                                <td colspan="5" class="py-24 text-center">
                                    <div class="flex flex-col items-center justify-center w-full">
                                        <span class="text-gray-400 italic text-sm tracking-wide">
                                            Belum ada konten audio yang ditambahkan.
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
                    @forelse($audios as $audio)
                    <div class="flex gap-3 border border-gray-100 rounded-xl p-3 bg-white">
                        <div class="shrink-0">
                            @if($audio->cover_url)
                            <img src="{{ $audio->cover_url }}" class="w-14 h-14 object-cover rounded-lg shadow-sm" alt="Cover">
                            @else
                            <div class="w-14 h-14 bg-gray-100 rounded-lg flex items-center justify-center text-gray-400 text-[10px]">No Cover</div>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-900 truncate">{{ $audio->title }}</p>
                            <div class="mt-1.5 flex flex-wrap items-center gap-1.5">
                                <span style="color: #E43371; background-color: #FFE5F0;" class="px-2 py-0.5 text-[11px] font-bold rounded-full tracking-tight">{{ $audio->category }}</span>
                                <span class="text-[11px] text-gray-500 font-medium">{{ $audio->duration }}</span>
                            </div>
                        </div>
                        <form action="{{ route('audio.destroy', $audio->id) }}" method="POST" class="shrink-0 self-start"
                            onsubmit="return confirm('Hapus audio ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-full transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </form>
                    </div>
                    @empty
                    <div class="py-16 text-center">
                        <span class="text-gray-400 italic text-sm tracking-wide">Belum ada konten audio yang ditambahkan.</span>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <x-modal name="tambah-audio" :show="$errors->any()" focusable>
        <form action="{{ route('audio.store') }}" method="POST" enctype="multipart/form-data" class="p-4 sm:p-6">
            @csrf
            <h2 class="text-lg font-medium text-gray-900">Unggah Konten Mindfulness</h2>
            <p class="mt-1 text-sm text-gray-600 italic">Konten ini akan muncul di aplikasi mobile pengguna.</p>

            <div class="mt-8 space-y-5">
                <div>
                    <x-input-label for="title" value="Judul" />
                    <x-text-input id="title" name="title" type="text" class="mt-2 block w-full text-sm"
                        placeholder="Masukkan judul" required />
                </div>

                <div>
                    <x-input-label for="category" value="Kategori" />
                    <select name="category"
                        class="mt-2 w-full border-gray-300 rounded-md shadow-sm focus:border-pink-500 focus:ring-pink-500 text-sm">
                        <option value="Relaksasi">Relaksasi</option>
                        <option value="Meditasi">Meditasi</option>
                        <option value="Tidur">Tidur</option>
                    </select>
                </div>

                <div>
                    <x-input-label for="description" value="Deskripsi" />
                    <textarea name="description" rows="3"
                        class="mt-2 w-full border-gray-300 rounded-md shadow-sm focus:border-pink-500 focus:ring-pink-500 text-sm"
                        placeholder="Masukkan deskripsi singkat"></textarea>
                </div>

                <div>
                    <x-input-label value="Foto Cover" />
                    <input type="file" name="cover_file" accept="image/*" class="mt-2 block w-full text-xs text-gray-500 
                        file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 
                        file:text-xs file:font-semibold file:bg-gray-100 file:text-gray-700 
                        hover:file:bg-gray-200 cursor-pointer" />
                </div>

                <div>
                    <x-input-label value="File Audio (MP3)" />
                    <input type="file" name="audio_file" accept="audio/mpeg" required class="mt-2 block w-full text-xs text-gray-500 
                        file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 
                        file:text-xs file:font-semibold file:bg-gray-100 file:text-gray-700 
                        hover:file:bg-gray-200 cursor-pointer" />
                </div>
            </div>

            <div class="mt-8 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Batal') }}
                </x-secondary-button>

                <x-primary-button class="ms-3 bg-[#D83A64] hover:bg-pink-700 uppercase">
                    {{ __('Simpan') }}
                </x-primary-button>
            </div>
        </form>
    </x-modal>
</x-app-layout>