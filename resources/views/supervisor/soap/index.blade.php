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
                <div class="mb-6 text-left">
                    <h3 class="text-lg font-medium text-gray-900">Catatan SOAP</h3>
                    <p class="text-sm text-gray-500">Lihat dan tinjau catatan SOAP yang dikirim oleh konselor.</p>
                </div>

                <div class="overflow-x-auto -mx-4 sm:mx-0 px-4 sm:px-0">
                    <table class="min-w-[640px] w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 text-xs uppercase text-gray-700">
                                <th class="p-4 border-b font-bold">Pasien</th>
                                <th class="p-4 border-b font-bold">Konselor</th>
                                <th class="p-4 border-b font-bold text-right">Dibuat Pada</th>
                                <th class="p-4 border-b text-center font-bold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($soaps as $soap)
                            <tr class="hover:bg-gray-50 transition-colors text-sm">
                                <td class="p-4 font-medium text-gray-900">
                                    {{ ucfirst($soap->patient->username) }}
                                </td>
                                <td class="p-4 text-gray-600">
                                    {{ ucfirst($soap->counselor->username) }}
                                </td>
                                <td class="p-4 text-gray-600 text-right">
                                    {{ $soap->created_at->format('d M Y') }}
                                </td>
                                <td class="p-4 text-center">
                                    <div class="flex flex-col sm:flex-row items-center justify-center gap-2">
                                        <x-primary-button
                                            type="button"
                                            x-data=""
                                            x-on:click.prevent="$dispatch('open-modal', 'detail-soap-{{ $soap->id }}')"
                                            class="bg-[#D83A64] hover:bg-pink-700 active:bg-pink-900 uppercase text-[10px] focus:ring-0 focus:ring-offset-0">
                                            {{ __('Lihat Detail') }}
                                        </x-primary-button>

                                        <form method="POST" action="{{ route('supervisor.soap.destroy', $soap->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus catatan ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <x-danger-button class="uppercase text-[10px] focus:ring-0 focus:ring-offset-0">
                                                {{ __('Hapus') }}
                                            </x-danger-button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                            <x-modal name="detail-soap-{{ $soap->id }}" focusable>
                                <div class="p-4 sm:p-6 text-gray-900">
                                    <h2 class="text-lg sm:text-xl font-bold border-b pb-3 text-gray-800 uppercase tracking-wide">Detail Catatan SOAP</h2>

                                    <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 text-[12px] text-gray-500 gap-2 mb-6">
                                        <p>Pasien: <span class="font-bold text-gray-800 text-[12px]">{{ ucfirst($soap->patient->username) }}</span></p>
                                        <p>Konselor: <span class="font-bold text-gray-800 text-[12px]">{{ ucfirst($soap->counselor->username) }}</span></p>
                                    </div>

                                    <div class="space-y-6">
                                        <div class="border-l-4 border-pink-200 pl-4 text-left">
                                            <h4 class="font-bold text-sm text-gray-800">Subjective (Subjektif)</h4>
                                            <p class="text-sm text-gray-600 mt-1 leading-relaxed">{{ $soap->subjective }}</p>
                                        </div>
                                        <div class="border-l-4 border-pink-300 pl-4 text-left">
                                            <h4 class="font-bold text-sm text-gray-800">Objective (Objektif)</h4>
                                            <p class="text-sm text-gray-600 mt-1 leading-relaxed">{{ $soap->objective }}</p>
                                        </div>
                                        <div class="border-l-4 border-pink-400 pl-4 text-left">
                                            <h4 class="font-bold text-sm text-gray-800">Assessment (Penilaian)</h4>
                                            <p class="text-sm text-gray-600 mt-1 leading-relaxed">{{ $soap->assessment }}</p>
                                        </div>
                                        <div class="border-l-4 border-pink-500 pl-4 text-left">
                                            <h4 class="font-bold text-sm text-gray-800">Plan (Rencana)</h4>
                                            <p class="text-sm text-gray-600 mt-1 leading-relaxed">{{ $soap->plan }}</p>
                                        </div>
                                    </div>

                                    <div class="mt-8 flex justify-end">
                                        <x-primary-button
                                            x-on:click="$dispatch('close')"
                                            class="ms-3 bg-[#D83A64] hover:bg-pink-700 uppercase focus:ring-0 focus:ring-offset-0">
                                            {{ __('Tutup') }}
                                        </x-primary-button>
                                    </div>
                                </div>
                            </x-modal>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>