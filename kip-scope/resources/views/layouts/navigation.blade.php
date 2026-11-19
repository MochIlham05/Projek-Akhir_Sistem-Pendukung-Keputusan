<nav x-data="{ open: false }" class="bg-gradient-to-r from-blue-950 via-blue-900 to-blue-800 text-white shadow-md border-b-2 border-yellow-400">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <!-- Logo & Brand -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 group">
                        <div class="w-9 h-9 rounded-lg bg-yellow-400 text-blue-950 font-black text-base flex items-center justify-center shadow-md group-hover:scale-105 transition-transform">
                            KS
                        </div>
                        <div class="flex flex-col">
                            <span class="font-extrabold text-lg tracking-wider text-white">KIP-SCOPE</span>
                            <span class="text-[10px] text-yellow-300 font-medium tracking-tight -mt-1">SPK AHP & MOORA</span>
                        </div>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-2 sm:-my-px sm:ms-10 sm:flex">
                    <a href="{{ route('dashboard') }}" 
                        class="inline-flex items-center px-3.5 py-2 rounded-md text-sm font-semibold transition-colors {{ request()->routeIs('dashboard') ? 'bg-blue-800/80 text-yellow-300 shadow-sm border border-yellow-400/50' : 'text-blue-100 hover:text-white hover:bg-blue-800/50' }}">
                        <svg class="w-4 h-4 mr-1.5 {{ request()->routeIs('dashboard') ? 'text-yellow-400' : 'text-blue-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                        {{ __('Dashboard') }}
                    </a>

                    <a href="{{ route('mahasiswa.index') }}" 
                        class="inline-flex items-center px-3.5 py-2 rounded-md text-sm font-semibold transition-colors {{ request()->routeIs('mahasiswa.*') ? 'bg-blue-800/80 text-yellow-300 shadow-sm border border-yellow-400/50' : 'text-blue-100 hover:text-white hover:bg-blue-800/50' }}">
                        <svg class="w-4 h-4 mr-1.5 {{ request()->routeIs('mahasiswa.*') ? 'text-yellow-400' : 'text-blue-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        {{ __('Data Mahasiswa') }}
                    </a>

                    <a href="{{ route('ahp.index') }}" 
                        class="inline-flex items-center px-3.5 py-2 rounded-md text-sm font-semibold transition-colors {{ request()->routeIs('ahp.*') ? 'bg-blue-800/80 text-yellow-300 shadow-sm border border-yellow-400/50' : 'text-blue-100 hover:text-white hover:bg-blue-800/50' }}">
                        <svg class="w-4 h-4 mr-1.5 {{ request()->routeIs('ahp.*') ? 'text-yellow-400' : 'text-blue-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        {{ __('Pembobotan AHP') }}
                    </a>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-1.5 border border-blue-700/60 text-xs font-semibold rounded-lg text-white bg-blue-950/60 hover:bg-blue-950 hover:border-yellow-400 focus:outline-none transition">
                            <div class="w-6 h-6 rounded-full bg-yellow-400 text-blue-950 flex items-center justify-center font-bold mr-2 text-[11px]">
                                {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                            </div>
                            <div>{{ Auth::user()->name ?? 'User' }}</div>

                            <div class="ms-1.5 text-yellow-300">
                                <svg class="fill-current h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')" class="text-xs">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();"
                                    class="text-xs text-rose-600 font-semibold">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-blue-200 hover:text-white hover:bg-blue-800 focus:outline-none transition">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-blue-950 border-t border-blue-800">
        <div class="pt-2 pb-3 space-y-1 px-2">
            <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-md text-sm font-semibold {{ request()->routeIs('dashboard') ? 'bg-blue-800 text-yellow-300' : 'text-blue-100' }}">
                {{ __('Dashboard') }}
            </a>
            <a href="{{ route('mahasiswa.index') }}" class="block px-3 py-2 rounded-md text-sm font-semibold {{ request()->routeIs('mahasiswa.*') ? 'bg-blue-800 text-yellow-300' : 'text-blue-100' }}">
                {{ __('Data Mahasiswa') }}
            </a>
            <a href="{{ route('ahp.index') }}" class="block px-3 py-2 rounded-md text-sm font-semibold {{ request()->routeIs('ahp.*') ? 'bg-blue-800 text-yellow-300' : 'text-blue-100' }}">
                {{ __('Pembobotan AHP') }}
            </a>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-3 border-t border-blue-900 px-4">
            <div class="font-medium text-sm text-yellow-300">{{ Auth::user()->name ?? 'User' }}</div>
            <div class="font-normal text-xs text-blue-200">{{ Auth::user()->email ?? '' }}</div>

            <div class="mt-3 space-y-1">
                <a href="{{ route('profile.edit') }}" class="block text-xs text-blue-200 hover:text-white py-1">
                    {{ __('Profile') }}
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();" class="block text-xs text-rose-300 hover:text-rose-100 py-1">
                        {{ __('Log Out') }}
                    </a>
                </form>
            </div>
        </div>
    </div>
</nav>
