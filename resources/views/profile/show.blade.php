<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-slate-100 leading-tight">
                {{ __('Profilim') }}
            </h2>
            <a href="{{ route('profile.edit') }}" class="inline-flex items-center px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-500 transition">
                {{ __('Profili Düzenle') }}
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <section class="bg-slate-800 border border-slate-700 rounded-2xl p-6 sm:p-8 shadow">
                <div class="flex flex-col sm:flex-row sm:items-center gap-6">
                    @if ($user->avatar_url)
                        <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-20 h-20 rounded-full object-cover shadow-lg ring-2 ring-slate-600">
                    @else
                        <div class="w-20 h-20 rounded-full bg-gradient-to-br from-indigo-500 to-violet-600 text-white font-bold text-3xl flex items-center justify-center shadow-lg">
                            {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($user->name, 0, 1)) }}
                        </div>
                    @endif

                    <div class="flex-1">
                        <h1 class="text-2xl font-bold text-white">{{ $user->name }}</h1>
                        <p class="text-slate-400 mt-1">{{ $user->email }}</p>
                        <p class="text-indigo-300 text-sm mt-2">{{ $user->level_title }} · {{ __('Seviye') }} {{ $user->level }}</p>
                    </div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-4 py-2 rounded-lg border border-rose-500/60 text-rose-300 hover:bg-rose-500/10 text-sm font-medium transition">
                            {{ __('Çıkış Yap') }}
                        </button>
                    </form>
                </div>
            </section>

            <section class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
                <article class="bg-slate-800 border border-slate-700 rounded-xl p-5">
                    <p class="text-slate-400 text-sm">{{ __('Toplam XP') }}</p>
                    <p class="text-2xl font-bold text-indigo-300 mt-2">{{ number_format($user->xp) }}</p>
                </article>
                <article class="bg-slate-800 border border-slate-700 rounded-xl p-5">
                    <p class="text-slate-400 text-sm">{{ __('Açılan Kapsül') }}</p>
                    <p class="text-2xl font-bold text-cyan-300 mt-2">{{ number_format($user->capsules_opened) }}</p>
                </article>
                <article class="bg-slate-800 border border-slate-700 rounded-xl p-5">
                    <p class="text-slate-400 text-sm">{{ __('Oluşturulan Kapsül') }}</p>
                    <p class="text-2xl font-bold text-violet-300 mt-2">{{ number_format($user->capsules_created) }}</p>
                </article>
                <article class="bg-slate-800 border border-slate-700 rounded-xl p-5">
                    <p class="text-slate-400 text-sm">{{ __('Kazanılan Rozet') }}</p>
                    <p class="text-2xl font-bold text-amber-300 mt-2">{{ number_format($user->badges_count) }}</p>
                </article>
            </section>

            <section class="bg-slate-800 border border-slate-700 rounded-2xl p-6 shadow">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-lg font-semibold text-slate-100">{{ __('Seviye İlerlemesi') }}</h3>
                    <span class="text-sm text-slate-300">%{{ $user->levelProgress() }}</span>
                </div>
                <div class="w-full bg-slate-700 rounded-full h-3 overflow-hidden">
                    <div class="h-3 bg-gradient-to-r from-indigo-500 to-violet-500 rounded-full" style="width: {{ $user->levelProgress() }}%"></div>
                </div>
                <p class="mt-3 text-sm text-slate-400">
                    {{ __('Bu seviyedeki unvanınız:') }} <span class="text-indigo-300 font-medium">{{ $user->level_title }}</span>
                </p>
            </section>
        </div>
    </div>
</x-app-layout>
