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
                        <h3 class="text-lg font-medium text-gray-900">Instrumen PHQ-9</h3>
                        <p class="text-sm text-gray-500">Kelola daftar pertanyaan dan opsi jawaban untuk skrining depresi.</p>
                    </div>
                    <x-primary-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'tambah-soal')" class="bg-[#D83A64] hover:bg-pink-700 uppercase">
                        Tambah Soal
                    </x-primary-button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse border border-gray-100">
                        <thead>
                            <tr class="bg-gray-50 text-xs uppercase text-gray-700">
                                <th class="p-4 border-b w-16 text-center font-bold">No</th>
                                <th class="p-4 border-b font-bold">Pertanyaan & Opsi Jawaban</th>
                                <th class="p-4 border-b text-center font-bold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($questions as $q)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="p-4 font-bold text-gray-900 text-center text-sm align-top pt-5">
                                    {{ $loop->iteration }}.
                                </td>
                                <td class="p-4 align-top">
                                    <div class="text-sm text-gray-800 font-semibold mb-3 leading-relaxed">
                                        {{ $q->question_text }}
                                    </div>

                                    <div class="space-y-1.5 ml-1">
                                        @if($q->options)
                                        @foreach($q->options as $opt)
                                        <div class="flex items-center text-[12px] text-gray-600">
                                            <span class="w-5 h-5 bg-gray-50 text-[#D83A64] border border-gray-200 flex items-center justify-center rounded-md text-[10px] font-bold mr-3 shrink-0">
                                                {{ $opt['score'] }}
                                            </span>
                                            <span>{{ $opt['text'] }}</span>
                                        </div>
                                        @endforeach
                                        @endif
                                    </div>
                                </td>
                                <td class="p-4 text-center align-top pt-4">
                                    <form action="{{ route('phq-questions.destroy', $q->id) }}" method="POST" onsubmit="return confirm('Hapus pertanyaan ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700 transition duration-150 p-2 hover:bg-red-50 rounded-full">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="p-10 text-center text-gray-400 italic text-sm">
                                    Belum ada soal yang ditambahkan.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <x-modal name="tambah-soal" :show="$errors->any()" focusable>
        <form action="{{ route('phq-questions.store') }}" method="POST" class="p-6 text-gray-900">
            @csrf
            <h2 class="text-lg font-medium">Tambah Pertanyaan PHQ-9</h2>
            <p class="mt-1 text-sm text-gray-600 italic">Isi pertanyaan dan tentukan jawaban untuk skor 0-3.</p>

            <div class="mt-6 space-y-5">
                <div>
                    <x-input-label for="question_text" value="Pertanyaan" />
                    <textarea id="question_text" name="question_text"
                        class="mt-1 w-full border-gray-300 focus:border-pink-500 focus:ring-pink-500 rounded-md shadow-sm text-sm"
                        rows="3" placeholder="Masukkan pertanyaan" required></textarea>
                </div>

                <div class="border-t pt-4">
                    <p class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-4">Opsi Jawaban</p>
                    <div class="space-y-3">
                        <div class="flex items-center gap-4">
                            <span class="w-12 text-left font-bold text-xs text-gray-500">Skor 0</span>
                            <x-text-input name="option_0" type="text" class="block w-full text-sm" value="Tidak sama sekali" required />
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="w-12 text-left font-bold text-xs text-gray-500">Skor 1</span>
                            <x-text-input name="option_1" type="text" class="block w-full text-sm" value="Beberapa hari" required />
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="w-12 text-left font-bold text-xs text-gray-500">Skor 2</span>
                            <x-text-input name="option_2" type="text" class="block w-full text-sm" value="Lebih dari separuh hari" required />
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="w-12 text-left font-bold text-xs text-gray-500">Skor 3</span>
                            <x-text-input name="option_3" type="text" class="block w-full text-sm" value="Hampir setiap hari" required />
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-8 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Batal') }}
                </x-secondary-button>

                <x-primary-button class="ms-3 bg-[#D83A64] hover:bg-pink-700 uppercase">
                    {{ __('Simpan Soal') }}
                </x-primary-button>
            </div>
        </form>
    </x-modal>
</x-app-layout>