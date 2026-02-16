<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6">
                <h3 class="text-2xl font-bold text-gray-900">Monitoring Chat</h3>
                <p class="text-sm text-gray-500">Akses dan tinjau percakapan antara konselor dan pasien.</p>
            </div>

            <div class="grid grid-cols-12 gap-6">
                <div class="col-span-4 bg-white shadow-sm rounded-lg overflow-hidden">
                    <div class="p-4 border-b bg-gray-50">
                        <h4 class="font-bold text-gray-700">Sesi Chat</h4>
                    </div>
                    <div class="divide-y divide-gray-100 max-h-[600px] overflow-y-auto">
                        @forelse($sessions as $session)
                        <a href="{{ route('supervisor.monitoring-chat', ['user_id' => $session->id]) }}"
                            class="block p-4 hover:bg-pink-50 transition {{ request('user_id') == $session->id ? 'bg-pink-50 border-l-4 border-[#D83A64]' : '' }}">
                            <div class="flex justify-between items-start">
                                <span class="font-bold text-gray-900">{{ $session->username }}</span>
                                <span class="text-[10px] text-gray-400">14:30</span>
                            </div>
                            <p class="text-xs text-gray-500 truncate mt-1">Klik untuk melihat riwayat pesan...</p>
                        </a>
                        @empty
                        <p class="p-4 text-center text-gray-400 text-sm">Tidak ada sesi chat.</p>
                        @endforelse
                    </div>
                </div>

                <div class="col-span-8 bg-white shadow-sm rounded-lg flex flex-col min-h-[600px]">
                    @if($selectedChat)
                    <div class="p-4 border-b bg-gray-50">
                        <h4 class="font-bold text-gray-900">{{ $sessions->find(request('user_id'))->username }}</h4>
                        <p class="text-[10px] text-gray-500">Monitoring Mode - Read Only</p>
                    </div>
                    <div class="flex-1 p-6 space-y-4 overflow-y-auto bg-[#F8F9FA]">
                        @foreach($selectedChat as $chat)
                        <div class="flex {{ $chat->sender->role_id == 2 ? 'justify-end' : 'justify-start' }}">
                            <div class="max-w-[70%] p-3 rounded-xl text-sm {{ $chat->sender->role_id == 2 ? 'bg-[#2563EB] text-white rounded-tr-none' : 'bg-white text-gray-800 shadow-sm rounded-tl-none' }}">
                                {{ $chat->message }}
                                <div class="text-[10px] mt-1 {{ $chat->sender->role_id == 2 ? 'text-blue-100' : 'text-gray-400' }}">
                                    {{ $chat->created_at->format('H:i') }}
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="flex-1 flex flex-col items-center justify-center text-gray-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mb-4 opacity-20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.274 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                        <p>Pilih sesi chat untuk melihat riwayat percakapan</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>