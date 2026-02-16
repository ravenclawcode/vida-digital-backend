<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-900">Catatan SOAP</h3>
                    <p class="text-sm text-gray-500">Lihat dan tinjau catatan SOAP yang dikirim oleh konselor.</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 text-xs uppercase text-gray-700">
                                <th class="p-4 border-b font-bold">Pasien</th>
                                <th class="p-4 border-b font-bold">Konselor</th>
                                <th class="p-4 border-b font-bold">Tanggal Sesi</th>
                                <th class="p-4 border-b font-bold">Waktu Sesi</th>
                                <th class="p-4 border-b text-center font-bold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($soaps as $soap)
                            <tr class="hover:bg-gray-50 transition-colors text-sm">
                                <td class="p-4 font-medium">{{ $soap->patient->username }}</td>
                                <td class="p-4 text-gray-600">{{ $soap->counselor->username }}</td>
                                <td class="p-4 text-gray-600">{{ $soap->created_at->format('Y-m-d') }}</td>
                                <td class="p-4 text-gray-600">{{ $soap->created_at->format('H:i') }} - Selesai</td>
                                <td class="p-4 text-center">
                                    <x-primary-button x-data=""
                                        x-on:click.prevent="$dispatch('open-modal', 'detail-soap-{{ $soap->id }}')"
                                        class="bg-[#2563EB] hover:bg-blue-700 text-[10px] py-1 px-3">
                                        Lihat Detail
                                    </x-primary-button>
                                </td>
                            </tr>

                            <x-modal name="detail-soap-{{ $soap->id }}" focusable>
                                <div class="p-6 text-gray-900">
                                    <h2 class="text-xl font-bold border-b pb-3">Detail Catatan SOAP</h2>
                                    <div class="mt-4 grid grid-cols-2 text-xs text-gray-500 gap-2 mb-6">
                                        <p>Pasien: <span class="font-bold text-gray-800">{{ $soap->patient->username }}</span></p>
                                        <p>Konselor: <span class="font-bold text-gray-800">{{ $soap->counselor->username }}</span></p>
                                    </div>

                                    <div class="space-y-6">
                                        <div class="border-l-4 border-blue-500 pl-4">
                                            <h4 class="font-bold text-sm">Subjective (Subjektif)</h4>
                                            <p class="text-sm text-gray-600 mt-1">{{ $soap->subjective }}</p>
                                        </div>
                                        <div class="border-l-4 border-green-500 pl-4">
                                            <h4 class="font-bold text-sm">Objective (Objektif)</h4>
                                            <p class="text-sm text-gray-600 mt-1">{{ $soap->objective }}</p>
                                        </div>
                                        <div class="border-l-4 border-orange-500 pl-4">
                                            <h4 class="font-bold text-sm">Assessment (Penilaian)</h4>
                                            <p class="text-sm text-gray-600 mt-1">{{ $soap->assessment }}</p>
                                        </div>
                                        <div class="border-l-4 border-purple-500 pl-4">
                                            <h4 class="font-bold text-sm">Plan (Rencana)</h4>
                                            <p class="text-sm text-gray-600 mt-1">{{ $soap->plan }}</p>
                                        </div>
                                    </div>

                                    <div class="mt-8 flex justify-end">
                                        <x-secondary-button x-on:click="$dispatch('close')">Tutup</x-secondary-button>
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