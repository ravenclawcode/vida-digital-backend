<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Konten Edukasi') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white p-6 shadow-sm sm:rounded-lg mb-8">
                <h3 class="text-lg font-bold mb-6">Tambah Konten Baru</h3>

                <form action="{{ route('education.store') }}" method="POST"
                    class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @csrf

                    <div>
                        <x-input-label value="Jenis Konten" />
                        <select name="type" id="type_selector" class="w-full border-gray-300 rounded-md shadow-sm"
                            onchange="updateForm()">
                            <option value="video">Video (YouTube)</option>
                            <option value="artikel">Artikel</option>
                        </select>
                    </div>

                    <div>
                        <x-input-label value="Kategori" />
                        <select name="category" class="w-full border-gray-300 rounded-md shadow-sm" required>
                            <option value="dasar">Dasar</option>
                            <option value="kesehatan mental">Kesehatan Mental</option>
                            <option value="gaya hidup">Gaya Hidup</option>
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <x-input-label value="Judul Konten" />
                        <x-text-input name="title" class="w-full" required />
                    </div>

                    <div id="video_fields" class="md:col-span-2">
                        <x-input-label value="URL Video YouTube" />
                        <x-text-input name="video_url" class="w-full"
                            placeholder="https://www.youtube.com/watch?v=xxxxxxx" />
                        <p class="mt-1 text-xs text-gray-500">
                            Thumbnail & durasi akan diambil otomatis dari YouTube
                        </p>
                    </div>

                    <div class="md:col-span-2">
                        <x-input-label id="label_description" value="Deskripsi Video" />
                        <textarea name="description" class="w-full border-gray-300 rounded-md shadow-sm" rows="8"
                            placeholder="Tulis di sini..." required></textarea>
                    </div>

                    <div id="artikel_fields" class="md:col-span-2 hidden space-y-4">
                        <div class="p-4 bg-pink-50 border border-pink-200 rounded-md">
                            <x-input-label value="Catatan Penting" class="text-pink-700 font-bold" />
                            <textarea name="important_note" class="w-full border-pink-300 rounded-md mt-1" rows="2"
                                placeholder="Pesan penting untuk pengguna (Opsional)"></textarea>
                        </div>
                    </div>

                    <div id="duration_field" class="hidden">
                        <x-input-label value="Estimasi Waktu Baca (Contoh: 5 Menit)" />
                        <x-text-input name="duration" class="w-full" placeholder="5 Menit" />
                    </div>

                    <div class="md:col-span-2 text-right mt-4">
                        <x-primary-button>
                            Simpan Konten Edukasi
                        </x-primary-button>
                    </div>
                </form>
            </div>

            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <h3 class="text-lg font-bold mb-4">Daftar Edukasi</h3>

                <div class="overflow-x-auto">
                    <table class="w-full border-collapse">
                        <thead>
                            <tr class="bg-gray-50 text-xs uppercase text-gray-600">
                                <th class="p-4 border-b text-left">Konten</th>
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
                                                            <form method="POST" action="{{ route('education.destroy', $content->id) }}"
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
                                    <td colspan="5" class="p-6 text-center text-gray-400 italic">
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

            const label = document.getElementById('label_description');
            if (isVideo) {
                label.innerText = "Deskripsi Video";
            } else {
                label.innerText = "Isi Lengkap Artikel";
            }
        }

        document.addEventListener('DOMContentLoaded', updateForm);
    </script>
</x-app-layout>