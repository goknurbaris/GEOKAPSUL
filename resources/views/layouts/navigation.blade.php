<nav x-data="{
    open: false,
    notificationsOpen: false,
    notifications: [],
    unreadCount: 0,
    loadingNotifications: false,
    notificationFilter: 'all',
    async loadNotifications() {
        this.loadingNotifications = true;
        try {
            const params = new URLSearchParams();
            if (this.notificationFilter !== 'all') params.set('filter', this.notificationFilter);
            const response = await fetch('{{ route('api.notifications') }}?' + params.toString(), {
                headers: { 'Accept': 'application/json' }
            });
            if (!response.ok) throw new Error('notifications fetch failed');
            const data = await response.json();
            this.notifications = data.items || [];
            this.unreadCount = data.unread_count || 0;
        } finally {
            this.loadingNotifications = false;
        }
    },
    async markAllNotificationsRead() {
        await fetch('{{ route('api.notifications.read-all') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || '',
                'Accept': 'application/json'
            }
        });
        this.unreadCount = 0;
        this.notifications = this.notifications.map(item => ({ ...item, read_at: item.read_at ?? new Date().toISOString() }));
    }
}" class="bg-slate-900 border-b border-slate-700">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-slate-200" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-3">
                <div class="relative">
                    <button @click="notificationsOpen = !notificationsOpen; if (notificationsOpen && notifications.length === 0) loadNotifications()"
                            class="relative inline-flex items-center justify-center w-10 h-10 rounded-xl border border-slate-700 bg-slate-800 text-slate-200 hover:bg-slate-700 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V4a2 2 0 10-4 0v1.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <span x-show="unreadCount > 0" class="absolute -top-1 -right-1 min-w-5 h-5 px-1 rounded-full bg-rose-500 text-white text-[10px] font-bold flex items-center justify-center" x-text="unreadCount"></span>
                    </button>

                    <div x-show="notificationsOpen" @click.outside="notificationsOpen = false" x-cloak
                         class="absolute right-0 mt-2 w-80 rounded-xl border border-slate-700 bg-slate-800 shadow-2xl z-50">
                        <div class="px-4 py-3 border-b border-slate-700 flex items-center justify-between">
                            <p class="text-sm font-semibold text-slate-100">Bildirimler</p>
                            <button @click="markAllNotificationsRead()" class="text-xs text-cyan-300 hover:text-cyan-200">Tümünü okundu yap</button>
                        </div>
                        <div class="px-4 py-2 border-b border-slate-700 flex items-center gap-2 text-xs">
                            <button @click="notificationFilter = 'all'; loadNotifications()" :class="notificationFilter === 'all' ? 'bg-slate-600 text-white' : 'text-slate-300'" class="px-2 py-1 rounded-md">Tümü</button>
                            <button @click="notificationFilter = 'unread'; loadNotifications()" :class="notificationFilter === 'unread' ? 'bg-slate-600 text-white' : 'text-slate-300'" class="px-2 py-1 rounded-md">Okunmamış</button>
                            <button @click="notificationFilter = 'capsule-created'; loadNotifications()" :class="notificationFilter === 'capsule-created' ? 'bg-slate-600 text-white' : 'text-slate-300'" class="px-2 py-1 rounded-md">Oluşturma</button>
                        </div>
                        <div class="max-h-80 overflow-auto">
                            <template x-if="loadingNotifications">
                                <p class="px-4 py-3 text-sm text-slate-400">Yükleniyor...</p>
                            </template>
                            <template x-if="!loadingNotifications && notifications.length === 0">
                                <p class="px-4 py-3 text-sm text-slate-400">Henüz bildirim yok.</p>
                            </template>
                            <template x-for="item in notifications" :key="item.id">
                                <a :href="item.action_url || '{{ route('dashboard') }}'" class="block px-4 py-3 border-b border-slate-700/60 hover:bg-slate-700/40">
                                    <div class="flex items-center justify-between gap-2">
                                        <p class="text-sm font-medium text-slate-100" x-text="item.title"></p>
                                        <span x-show="!item.read_at" class="text-[10px] px-1.5 py-0.5 rounded bg-rose-500/20 text-rose-300">Yeni</span>
                                    </div>
                                    <p class="text-xs text-slate-400 mt-1" x-text="item.body"></p>
                                    <p class="text-[10px] text-slate-500 mt-1" x-text="new Date(item.created_at).toLocaleString('tr-TR')"></p>
                                </a>
                            </template>
                        </div>
                    </div>
                </div>

                <x-dropdown align="right" width="64" contentClasses="py-2 bg-slate-800 border border-slate-700">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-3 px-3 py-2 border border-slate-700 text-sm leading-4 font-medium rounded-xl text-slate-200 bg-slate-800 hover:bg-slate-700 focus:outline-none transition ease-in-out duration-150">
                            @if (Auth::user()->avatar_url)
                                <img src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->name }}" loading="lazy" class="w-9 h-9 rounded-full object-cover ring-2 ring-slate-600">
                            @else
                                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-indigo-500 to-violet-600 text-white flex items-center justify-center font-semibold">
                                    {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr(Auth::user()->name, 0, 1)) }}
                                </div>
                            @endif

                            <div class="text-start">
                                <p class="text-sm font-semibold text-slate-100">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-slate-400">{{ Auth::user()->email }}</p>
                            </div>

                            <div class="ms-1 text-slate-400">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.show')" class="text-slate-200 hover:bg-slate-700 focus:bg-slate-700">
                            {{ __('Profil Sayfam') }}
                        </x-dropdown-link>

                        <x-dropdown-link :href="route('profile.edit')" class="text-slate-200 hover:bg-slate-700 focus:bg-slate-700">
                            {{ __('Profil Ayarları') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    class="text-rose-300 hover:bg-rose-500/10 focus:bg-rose-500/10"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Çıkış Yap') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-slate-400 hover:text-slate-200 hover:bg-slate-800 focus:outline-none focus:bg-slate-800 focus:text-slate-200 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-slate-700 bg-slate-900">
            <div class="px-4">
                <div class="font-medium text-base text-slate-100">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-slate-400">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.show')" :active="request()->routeIs('profile.show')">
                    {{ __('Profil Sayfam') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profil Ayarları') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Çıkış Yap') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
