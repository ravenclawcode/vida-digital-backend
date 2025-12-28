<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Statistik Vida Digital') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    {{ __("Selamat Datang, Admin!") }} 
                    <p class="text-sm text-gray-500">Pantau kondisi psikologis komunitas Anda di bawah ini.</p>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">
                        Tren Mood Komunitas (7 Hari Terakhir)
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-5">
                            @forelse($moodStats as $stat)
                                @php
                                    $percentage = $totalMoods > 0 ? round(($stat->total / $totalMoods) * 100) : 0;
                                    // Warna progress bar berdasarkan mood
                                    $barColor = match($stat->mood_code) {
                                        'senang' => 'bg-green-400',
                                        'tenang' => 'bg-blue-400',
                                        'sedih'  => 'bg-yellow-500',
                                        'cemas'  => 'bg-orange-500',
                                        'lelah'  => 'bg-purple-500',
                                        default  => 'bg-gray-400'
                                    };
                                @endphp
                                <div>
                                    <div class="flex justify-between mb-1">
                                        <span class="text-sm font-semibold capitalize text-gray-700">
                                            {{ $stat->mood_code }}
                                        </span>
                                        <span class="text-sm font-bold text-gray-600">{{ $percentage }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-100 rounded-full h-3">
                                        <div class="{{ $barColor }} h-3 rounded-full transition-all duration-500" 
                                             style="width: {{ $percentage }}%"></div>
                                    </div>
                                </div>
                            @empty
                                <div class="flex flex-col items-center py-10">
                                    <p class="text-gray-400 italic">Belum ada data mood masuk minggu ini.</p>
                                </div>
                            @endforelse
                        </div>

                        <div class="bg-indigo-50 p-6 rounded-2xl border border-indigo-100 flex flex-col justify-center">
                            <h4 class="font-bold text-indigo-900 mb-2 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Insight Kesehatan Mental
                            </h4>
                            <p class="text-sm text-indigo-800 leading-relaxed">
                                Data di samping menunjukkan distribusi perasaan pengguna <strong>Vida Digital</strong> secara anonim. 
                                <br><br>
                                Jika persentase <strong>"Cemas"</strong> atau <strong>"Sedih"</strong> mendominasi, disarankan untuk memperbanyak konten edukasi atau audio mindfulness baru guna memberikan dukungan tambahan bagi penyintas.
                            </p>
                            <div class="mt-4 pt-4 border-t border-indigo-200">
                                <p class="text-xs text-indigo-600 font-medium">Total Input Mood: {{ $totalMoods }} kali</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>