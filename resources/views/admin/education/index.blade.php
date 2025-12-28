<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Konten Edukasi') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- SUCCESS --}}
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            {{-- FORM --}}
            <div class="bg-white p-6 shadow-sm sm:rounded-lg mb-8">
                <h3 class="text-lg font-bold mb-6">Tambah Konten Baru</h3>

                <form action="{{ route('education.store') }}" method="POST"
                      class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @csrf

                    {{-- TYPE --}}
                    <div>
                        <x-input-label value="Jenis Konten" />
                        <select name="type" id="type_selector"
                                class="w-full border-gray-300 rounded-md shadow-sm"
                                onchange="updateForm()">
                            <option value="video">Video (YouTube)</option>
                            <option value="artikel">Artikel</option>
                        </select>
                    </div>

                    {{-- CATEGORY --}}
                    <div>
                        <x-input-label value="Kategori" />
                        <x-text-input name="category" class="w-full" required />
                    </div>

                    {{-- TITLE --}}
                    <div class="md:col-span-2">
                        <x-input-label value="Judul Konten" />
                        <x-text-input name="title" class="w-full" required />
                    </div>

                    {{-- VIDEO FIELDS --}}
                    <div id="video_fields" class="md:col-span-2 space-y-4">
                        <div>
                            <x-input-label value="URL Video YouTube" />
                            <x-text-input
                                name="video_url"
                                class="w-full"
                                placeholder="https://www.youtube.com/watch?v=xxxxxxx" />
                            <p class="mt-1 text-xs text-gray-500">
                                Thumbnail & durasi akan diambil otomatis
                            </p>
                        </div>

                        <div>
                            <x-input-label value="Deskripsi Video" />
                            <textarea name="description"
                                class="w-full border-gray-300 rounded-md shadow-sm"
                                rows="3"
                                placeholder="Ringkasan singkat isi video"></textarea>
                        </div>
                    </div>

                    {{-- ARTIKEL FIELDS --}}
                    <div id="artikel_fields" class="md:col-span-2 hidden space-y-4">
                        <div>
                            <x-input-label value="Isi Artikel" />
                            <textarea name="content"
                                class="w-full border-gray-300 rounded-md shadow-sm"
                                rows="6"
                                placeholder="Tulis artikel di sini..."></textarea>
                        </div>

                        <div class="p-4 bg-pink-50 border border-pink-200 rounded-md">
                            <x-input-label
                                value="Catatan Penting"
                                class="text-pink-700 font-bold" />
                            <textarea name="important_note"
                                class="w-full border-pink-300 rounded-md mt-1"
                                rows="2"
                                placeholder="Pesan penting untuk pengguna"></textarea>
                        </div>
                    </div>

                    {{-- DURASI (HANYA ARTIKEL) --}}
                    <div id="duration_field" class="hidden">
                        <x-input-label value="Estimasi Waktu Baca (Contoh: 5 Menit)" />
                        <x-text-input
                            name="duration"
                            class="w-full"
                            placeholder="5 Menit" />
                    </div>

                    {{-- SUBMIT --}}
                    <div class="md:col-span-2 text-right mt-4">
                        <x-primary-button>
                            Simpan Konten Edukasi
                        </x-primary-button>
                    </div>
                </form>
            </div>

            {{-- TABLE --}}
            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <h3 class="text-lg font-bold mb-4">Daftar Edukasi</h3>

                <div class="overflow-x-auto">
                    <table class="w-full border-collapse">
                        <thead>
                            <tr class="bg-gray-50 text-xs uppercase text-gray-600">
                                <th class="p-4 border-b">Konten</th>
                                <th class="p-4 border-b text-center">Tipe</th>
                                <th class="p-4 border-b text-center">Durasi</th>
                                <th class="p-4 border-b text-center">Likes</th>
                                <th class="p-4 border-b text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @forelse($contents as $content)
                                <tr class="hover:bg-gray-50">
                                    <td class="p-4">
                                        <div class="font-bold text-gray-900">
                                            {{ $content->title }}
                                        </div>
                                        <div class="text-xs text-indigo-600">
                                            {{ $content->category }}
                                        </div>
                                    </td>

                                    <td class="p-4 text-center">
                                        <span class="px-2 py-1 text-xs font-bold rounded-full
                                            {{ $content->type === 'video'
                                                ? 'bg-red-100 text-red-700'
                                                : 'bg-green-100 text-green-700' }}">
                                            {{ strtoupper($content->type) }}
                                        </span>
                                    </td>

                                    <td class="p-4 text-center text-sm">
                                        {{ $content->duration }}
                                    </td>

                                    <td class="p-4 text-center font-bold text-pink-500">
                                        {{ number_format($content->likes ?? 0) }}
                                    </td>

                                    <td class="p-4 text-center">
                                        <form method="POST"
                                              action="{{ route('education.destroy', $content->id) }}"
                                              onsubmit="return confirm('Hapus konten ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="text-red-500 hover:text-red-700">
                                                🗑️
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5"
                                        class="p-6 text-center text-gray-400 italic">
                                        Belum ada konten edukasi.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- SCRIPT --}}
    <script>
        function updateForm() {
            const type = document.getElementById('type_selector').value;
            const isVideo = type === 'video';

            document.getElementById('video_fields')
                .classList.toggle('hidden', !isVideo);

            document.getElementById('artikel_fields')
                .classList.toggle('hidden', isVideo);

            document.getElementById('duration_field')
                .classList.toggle('hidden', isVideo);
        }

        document.addEventListener('DOMContentLoaded', updateForm);
    </script>
</x-app-layout>
