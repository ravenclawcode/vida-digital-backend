<x-app-layout>
    <div class="py-8 bg-[#F8F9FA] min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900">Monitoring Chat</h1>
                <p class="text-gray-500 text-sm">Akses dan tinjau percakapan antara konselor dan pasien</p>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 mb-6 overflow-hidden">
                <form action="{{ route('supervisor.monitoring-chat') }}" method="GET">
                    <div class="grid grid-cols-12 gap-0">
                        <div class="col-span-12 lg:col-span-4 p-5">
                            <label class="block text-[10px] font-bold text-gray-400 uppercase mb-2 tracking-wider">Pilih Konselor</label>
                            <select name="counselor_id" onchange="this.form.submit()"
                                class="w-full bg-gray-50 border-none rounded-xl focus:ring-2 focus:ring-pink-500 text-sm text-gray-600 py-2.5">
                                <option value="">Semua Konselor</option>
                                @foreach($counselors as $counselor)
                                <option value="{{ $counselor->id }}" {{ $selectedCounselorId == $counselor->id ? 'selected' : '' }}>
                                    {{ ucfirst($counselor->username) }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-span-12 lg:col-span-8 p-5">
                            <label class="block text-[10px] font-bold text-gray-400 uppercase mb-2 tracking-wider">Cari Pasien</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center">
                                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </span>
                                <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari berdasarkan nama pasien..."
                                    class="w-full bg-gray-50 border-none rounded-xl pl-10 py-2.5 text-sm focus:ring-2 focus:ring-pink-500">
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="grid grid-cols-12 gap-6">
                <div class="col-span-12 lg:col-span-4">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col h-[600px]">
                        <div class="p-5 border-b border-gray-50 bg-white">
                            <h2 class="font-bold text-gray-800 tracking-tight">Sesi Chat</h2>
                        </div>

                        <div class="flex-1 overflow-y-auto">
                            @forelse($sessions as $session)
                            @php
                            $isActive = (request('user_id') == $session->patient_id && request('active_counselor_id') == $session->counselor_id);
                            @endphp

                            <a href="{{ route('supervisor.monitoring-chat', [
                            'counselor_id' => $selectedCounselorId, 
                            'user_id' => $session->patient_id, 
                            'active_counselor_id' => $session->counselor_id, 
                            'search' => $search ?? ''
                        ]) }}"
                                class="block p-5 transition-all duration-200 border-l-4 border-b border-gray-50 {{ $isActive ? 'bg-pink-50 border-l-[#D94274]' : 'bg-white border-l-transparent hover:bg-gray-50' }}">

                                <div class="flex justify-between items-start mb-1">
                                    <span class="font-bold {{ $isActive ? 'text-[#D94274]' : 'text-gray-900' }} text-sm">
                                        {{ ucfirst($session->patient_name) }}
                                        <span class="mx-1 text-gray-400 font-normal">-</span>
                                        <span class="{{ $isActive ? 'text-gray-500' : 'text-gray-500' }} font-medium text-xs">{{ ucfirst($session->counselor_name) }}</span>
                                    </span>
                                    <span class="text-[10px] text-gray-400">{{ $session->last_time }}</span>
                                </div>

                                <div class="flex justify-between items-center mt-1">
                                    <p class="text-[13px] text-gray-500 truncate w-3/4">{{ $session->last_message }}</p>
                                    <span class="text-[9px] px-2 py-0.5 rounded-full font-bold {{ $isActive ? 'bg-[#D94274] text-white' : 'bg-gray-100 text-gray-600' }}">
                                        {{ $session->total_messages }} Pesan
                                    </span>
                                </div>
                            </a>
                            @empty
                            <div class="flex items-center justify-center h-full p-8">
                                <p class="text-gray-400 text-sm">Tidak ada sesi chat.</p>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="col-span-12 lg:col-span-8">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 flex flex-col h-[600px] overflow-hidden">
                        @if($selectedChat && $activePatient)
                        <div class="p-5 border-b border-gray-50 flex items-center justify-between bg-white z-10">
                            <div>
                                <h3 class="font-normal text-gray-900 text-lg">{{ ucfirst($activePatient->username) }}</h3>
                                <p class="text-[13px] text-gray-400 font-normal">
                                    Konselor: <span class="text font-normal">{{ ucfirst($counselors->find(request('active_counselor_id'))->username ?? 'Semua') }}</span> • {{ now()->format('d M Y') }}
                                </p>
                            </div>
                        </div>

                        <div class="flex-1 p-8 overflow-y-auto space-y-6 bg-white">
                            @foreach($selectedChat as $chat)
                            @php $isCounselor = ($chat->sender->role_id == 2); @endphp
                            <div class="flex {{ $isCounselor ? 'justify-end' : 'justify-start' }}">
                                <div class="max-w-[80%] lg:max-w-[70%]">
                                    <div class="p-4 rounded-[20px] text-sm {{ $isCounselor 
                                    ? 'bg-[#D94274] text-white rounded-br-none' 
                                    : 'bg-gray-100 text-gray-800 rounded-bl-none' }}">
                                        {{ $chat->message }}
                                    </div>
                                    <p class="text-[10px] mt-2 text-gray-400 {{ $isCounselor ? 'text-right' : 'text-left' }}">
                                        {{ $chat->created_at->format('H:i') }}
                                    </p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="flex-1 flex flex-col items-center justify-center text-gray-400">
                            <p>Pilih sesi chat untuk memantau percakapan</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>
    </div>
</x-app-layout>