<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Audio Mindfulness') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-sm sm:rounded-lg">

                {{-- SUCCESS MESSAGE --}}
                @if(session('success'))
                    <div class="mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700">
                        {{ session('success') }}
                    </div>
                @endif

                {{-- FORM UPLOAD AUDIO --}}
                <form action="{{ route('audio.store') }}"
                      method="POST"
                      enctype="multipart/form-data"
                      class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                    @csrf

                    <div>
                        <x-input-label value="Judul Audio" />
                        <x-text-input name="title"
                                      class="w-full"
                                      placeholder="Contoh: Relaksasi Pagi Hari"
                                      required />
                    </div>

                    <div>
                        <x-input-label value="Kategori" />
                        <select name="category"
                                class="w-full border-gray-300 rounded-md shadow-sm
                                       focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="Relaksasi">Relaksasi</option>
                            <option value="Meditasi">Meditasi</option>
                            <option value="Tidur">Tidur</option>
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <x-input-label value="Deskripsi Singkat" />
                        <textarea name="description"
                                  rows="2"
                                  class="w-full border-gray-300 rounded-md shadow-sm
                                         focus:border-indigo-500 focus:ring-indigo-500"
                                  placeholder="Mulai hari dengan tenang..."></textarea>
                    </div>

                    {{-- COVER --}}
                    <div class="md:col-span-2">
                        <x-input-label value="Foto Cover (Opsional)" />
                        <input type="file"
                               name="cover_file"
                               accept="image/*"
                               class="mt-1 block w-full text-sm text-gray-500
                                      file:mr-4 file:py-2 file:px-4
                                      file:rounded-full file:border-0
                                      file:text-sm file:font-semibold
                                      file:bg-pink-50 file:text-pink-700
                                      hover:file:bg-pink-100" />
                        <p class="mt-1 text-xs text-gray-500">
                            JPG / PNG • Maks 2MB
                        </p>
                    </div>

                    {{-- AUDIO --}}
                    <div class="md:col-span-2">
                        <x-input-label value="File Audio (MP3)" />
                        <input type="file"
                               name="audio_file"
                               accept="audio/mpeg"
                               required
                               class="mt-1 block w-full text-sm text-gray-500
                                      file:mr-4 file:py-2 file:px-4
                                      file:rounded-full file:border-0
                                      file:text-sm file:font-semibold
                                      file:bg-indigo-50 file:text-indigo-700
                                      hover:file:bg-indigo-100" />
                        <p class="mt-1 text-xs text-gray-500">
                            Format MP3 • Maks 40MB • Durasi terdeteksi otomatis
                        </p>
                    </div>

                    <div class="md:col-span-2">
                        <x-primary-button>
                            {{ __('Simpan & Unggah Audio') }}
                        </x-primary-button>
                    </div>
                </form>

                <hr class="my-6">

                {{-- TABLE --}}
                <div class="overflow-x-auto">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        Daftar Audio
                    </h3>

                    <table class="w-full text-left border-collapse border border-gray-100">
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

                                    {{-- COVER COLUMN --}}
                                    <td class="p-4 border-b text-center">
                                        @if($audio->cover_url)
                                            <img src="{{ $audio->cover_url }}"
                                                 class="w-10 h-10 object-cover rounded-md mx-auto shadow"
                                                 alt="Cover">
                                        @else
                                            <div class="w-10 h-10 bg-gray-100 rounded-md
                                                        flex items-center justify-center
                                                        text-gray-400 text-xs mx-auto">
                                                N/A
                                            </div>
                                        @endif
                                    </td>

                                    {{-- TITLE --}}
                                    <td class="p-4 border-b font-medium text-gray-900">
                                        {{ $audio->title }}
                                        <div class="text-xs text-gray-400 truncate max-w-xs">
                                            {{ $audio->audio_url }}
                                        </div>
                                    </td>

                                    {{-- CATEGORY --}}
                                    <td class="p-4 border-b text-center">
                                        <span class="px-2 py-1 text-xs font-semibold
                                                     bg-blue-100 text-blue-700 rounded-full">
                                            {{ $audio->category }}
                                        </span>
                                    </td>

                                    {{-- DURATION --}}
                                    <td class="p-4 border-b text-center text-sm text-gray-600">
                                        {{ $audio->duration }}
                                    </td>

                                    {{-- ACTION --}}
                                    <td class="p-4 border-b text-center">
                                        <form action="{{ route('audio.destroy', $audio->id) }}"
                                              method="POST"
                                              onsubmit="return confirm('Hapus audio ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="text-red-500 hover:text-red-700 transition">
                                                🗑
                                            </button>
                                        </form>
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5"
                                        class="p-8 text-center text-gray-400 italic">
                                        Belum ada konten audio
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
