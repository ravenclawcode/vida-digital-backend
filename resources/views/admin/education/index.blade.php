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
                        <h3 class="text-lg font-medium text-gray-900">Daftar Edukasi</h3>
                        <p class="text-sm text-gray-500">Kelola konten video dan artikel kesehatan mental.</p>
                    </div>

                    <x-primary-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'tambah-edukasi')"
                        class="bg-[#D83A64] hover:bg-pink-700 uppercase w-full sm:w-auto justify-center">
                        {{ __('Tambah Konten') }}
                    </x-primary-button>
                </div>

                <div class="overflow-x-auto -mx-4 sm:mx-0 px-4 sm:px-0">
                    <table class="min-w-[720px] w-full text-left border-collapse border border-gray-100">
                        <thead>
                            <tr class="bg-gray-50 text-gray-700 uppercase text-xs">
                                <th class="p-4 border-b text-left">Judul Konten</th>
                                <th class="p-4 border-b text-center">Tipe</th>
                                <th class="p-4 border-b text-center">Kategori</th>
                                <th class="p-4 border-b text-center">Durasi</th>
                                <th class="p-4 border-b text-center">Likes</th>
                                <th class="p-4 border-b text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($contents as $content)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="p-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $content->title }}</div>
                                </td>

                                <td class="p-4 text-center">
                                    <span class="text-sm text-gray-900 font-medium">
                                        {{ ucfirst($content->type) }}
                                    </span>
                                </td>

                                <td class="p-4 text-center">
                                    <span style="color: #E43371; background-color: #FFE5F0;"
                                        class="px-3 py-1 text-xs font-bold rounded-full tracking-tight">
                                        {{ Str::title($content->category) }}
                                    </span>
                                </td>

                                <td class="p-4 text-center text-sm text-gray-600 font-medium">
                                    {{ $content->duration }}
                                </td>

                                <td class="p-4 text-center text-sm text-gray-900 font-medium">
                                    {{ number_format($content->likes ?? 0) }}
                                </td>

                                <td class="p-4 text-center">
                                    <form method="POST" action="{{ route('education.destroy', $content->id) }}"
                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus konten ini?')">
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
                                <td colspan="6" class="py-24 text-center">
                                    <div class="flex flex-col items-center justify-center w-full">
                                        <span class="text-gray-400 italic text-sm tracking-wide">
                                            Belum ada konten edukasi yang ditambahkan.
                                        </span>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <x-modal name="tambah-edukasi" :show="$errors->any()" focusable>
        <form action="{{ route('education.store') }}" method="POST" class="p-4 sm:p-6">
            @csrf
            <h2 class="text-lg font-medium text-gray-900">Tambah Konten Edukasi Baru</h2>
            <p class="mt-1 text-sm text-gray-600 italic">Pilih jenis konten (Video/Artikel) dan lengkapi detailnya.</p>

            <div class="mt-8 space-y-5">
                <div>
                    <x-input-label for="type_selector" value="Jenis Konten" />
                    <select name="type" id="type_selector"
                        class="mt-2 w-full border-gray-300 rounded-md shadow-sm focus:border-pink-500 focus:ring-pink-500 text-sm"
                        onchange="updateForm()">
                        <option value="video">Video</option>
                        <option value="artikel">Artikel</option>
                    </select>
                </div>

                <div>
                    <x-input-label for="category" value="Kategori" />
                    <select name="category"
                        class="mt-2 w-full border-gray-300 rounded-md shadow-sm focus:border-pink-500 focus:ring-pink-500 text-sm"
                        required>
                        <option value="dasar">Dasar</option>
                        <option value="kesehatan mental">Kesehatan Mental</option>
                        <option value="gaya hidup">Gaya Hidup</option>
                    </select>
                </div>

                <div>
                    <x-input-label for="title" value="Judul" />
                    <x-text-input name="title" type="text" class="mt-2 block w-full text-sm"
                        placeholder="Masukkan judul" required />
                </div>

                <div id="video_fields">
                    <x-input-label for="video_url" value="URL Video YouTube" />
                    <x-text-input name="video_url" type="text" class="mt-2 block w-full text-sm"
                        placeholder="https://www.youtube.com/watch?v=xxxxxxx" />
                </div>

                <div>
                    <x-input-label id="label_description" value="Deskripsi" />
                    <textarea name="description" id="main_content_area"
                        class="mt-2 w-full border-gray-300 rounded-md shadow-sm focus:border-pink-500 focus:ring-pink-500 text-sm"
                        rows="5" placeholder="Masukkan deskripsi singkat atau isi artikel" required></textarea>
                </div>

                <div id="artikel_fields" class="hidden space-y-5">
                    <div>
                        <x-input-label for="important_note" value="Catatan Penting (Opsional)" />
                        <textarea name="important_note" id="important_note"
                            class="mt-2 w-full border-gray-300 rounded-md shadow-sm focus:border-pink-500 focus:ring-pink-500 text-sm"
                            rows="2" placeholder="Pesan tambahan untuk pembaca"></textarea>
                    </div>

                    <div id="duration_field">
                        <x-input-label for="duration" value="Estimasi Waktu Baca" />
                        <x-text-input name="duration" type="text" class="mt-2 block w-full text-sm"
                            placeholder="7 Menit" />
                    </div>
                </div>
            </div>

            <div class="mt-8 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">Batal</x-secondary-button>
                <x-primary-button class="ms-3 bg-[#D83A64] hover:bg-pink-700 uppercase">Simpan</x-primary-button>
            </div>
        </form>
    </x-modal>

    <script>
        function updateForm() {
            const typeSelector = document.getElementById('type_selector');
            const type = typeSelector.value;
            const isVideo = type === 'video';
            const label = document.getElementById('label_description');
            const textarea = document.getElementById('main_content_area');
            document.getElementById('video_fields').classList.toggle('hidden', !isVideo);
            document.getElementById('artikel_fields').classList.toggle('hidden', isVideo);
            if (isVideo) {
                label.innerText = "Deskripsi";
                textarea.placeholder = "Masukkan deskripsi singkat";
            } else {
                label.innerText = "Isi Artikel";
                textarea.placeholder = "Masukkan isi artikel";
            }
        }
        document.addEventListener('DOMContentLoaded', updateForm);
    </script>
</x-app-layout>