<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ auth()->user()->role_id == 4 ? route('supervisor.dashboard') : route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="auth()->user()->role_id == 4 ? route('supervisor.dashboard') : route('dashboard')"
                        :active="request()->routeIs('dashboard') || request()->routeIs('supervisor.dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    @if(auth()->user()->role_id == 1)
                    @php
                    $userActive = request()->routeIs('supervisor-management.*') || request()->routeIs('counselors.*') || request()->routeIs('tokens.*');
                    @endphp
                    <div class="hidden sm:flex sm:items-center sm:ms-4 relative">
                        <x-dropdown align="left" width="48">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center px-1 pt-1 border-b-2 {{ $userActive ? 'border-[#D83A64] text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} text-sm font-medium leading-5 transition duration-150 ease-in-out focus:outline-none h-16">
                                    <span>Manajemen Pengguna</span>
                                    <svg class="ms-2 h-4 w-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </x-slot>
                            <x-slot name="content">
                                <x-dropdown-link :href="route('supervisor-management.index')">Supervisor</x-dropdown-link>
                                <x-dropdown-link :href="route('counselors.index')">Konselor</x-dropdown-link>
                                <div class="border-t border-gray-100"></div>
                                <x-dropdown-link :href="route('tokens.index')">Token Registrasi User</x-dropdown-link>
                            </x-slot>
                        </x-dropdown>
                    </div>

                    @php
                    $contentActive = request()->routeIs('audio.*') || request()->routeIs('education.*') || request()->routeIs('phq-questions.*');
                    @endphp
                    <div class="hidden sm:flex sm:items-center sm:ms-4 relative">
                        <x-dropdown align="left" width="48">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center px-1 pt-1 border-b-2 {{ $contentActive ? 'border-[#D83A64] text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} text-sm font-medium leading-5 transition duration-150 ease-in-out focus:outline-none h-16">
                                    <span>Manajemen Konten</span>
                                    <svg class="ms-2 h-4 w-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </x-slot>
                            <x-slot name="content">
                                <x-dropdown-link :href="route('audio.index')">Audio</x-dropdown-link>
                                <x-dropdown-link :href="route('education.index')">Edukasi</x-dropdown-link>
                                <x-dropdown-link :href="route('phq-questions.index')">Instrumen PHQ-9</x-dropdown-link>
                            </x-slot>
                        </x-dropdown>
                    </div>
                    @endif

                    @if(auth()->user()->role_id == 4)
                    <x-nav-link :href="route('supervisor.monitoring-chat')" :active="request()->routeIs('supervisor.monitoring-chat')">
                        Monitoring Chat
                    </x-nav-link>
                    <x-nav-link :href="route('supervisor.catatan-soap')" :active="request()->routeIs('supervisor.catatan-soap')">
                        Catatan SOAP
                    </x-nav-link>
                    @endif
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->username }}</div>
                            <svg class="ms-1 h-4 w-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">Profil Saya</x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">Keluar</x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>
        </div>
    </div>
</nav>