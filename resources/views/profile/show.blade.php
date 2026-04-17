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
                        <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" loading="lazy" class="w-20 h-20 rounded-full object-cover shadow-lg ring-2 ring-slate-600">
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

            <section class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                <article class="xl:col-span-2 bg-slate-800 border border-slate-700 rounded-2xl p-6 shadow">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-slate-100">{{ __('Son Kazanılan Rozetler') }}</h3>
                        <span class="text-xs text-slate-400">{{ __('Toplam') }}: {{ $user->badges_count }}</span>
                    </div>

                    @if($recentBadges->isEmpty())
                        <p class="text-sm text-slate-400">{{ __('Henüz rozet kazanmadın. Kapsül oluşturarak ilk rozetine ulaşabilirsin.') }}</p>
                    @else
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                            @foreach($recentBadges as $badge)
                                @php($classes = $badge->color_classes)
                                <div class="rounded-xl border border-slate-600/70 bg-slate-900/60 p-4">
                                    <div class="w-10 h-10 rounded-full {{ $classes[0] }} flex items-center justify-center text-lg shadow {{ $classes[2] }}">
                                        {{ $badge->icon }}
                                    </div>
                                    <p class="mt-3 text-sm font-semibold text-slate-100">{{ $badge->name }}</p>
                                    <p class="text-xs text-slate-400 mt-1">{{ $badge->description }}</p>
                                    <p class="text-[11px] text-slate-500 mt-2">
                                        {{ __('Kazanıldı:') }} {{ optional($badge->pivot->earned_at)->format('d.m.Y') ?? '—' }}
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </article>

                <article class="bg-slate-800 border border-slate-700 rounded-2xl p-6 shadow">
                    <h3 class="text-lg font-semibold text-slate-100 mb-4">{{ __('Kategori Dağılımı') }}</h3>
                    @if(empty($capsulesByCategory))
                        <p class="text-sm text-slate-400">{{ __('Henüz kapsül oluşturmamışsın.') }}</p>
                    @else
                        <div class="space-y-3">
                            @foreach($capsulesByCategory as $category => $count)
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-slate-300">{{ \App\Models\Capsule::CATEGORIES[$category]['name'] ?? ucfirst($category) }}</span>
                                    <span class="text-cyan-300 font-semibold">{{ $count }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </article>
            </section>

            <section class="bg-slate-800 border border-slate-700 rounded-2xl p-6 shadow">
                <h3 class="text-lg font-semibold text-slate-100 mb-3">{{ __('Sıradaki Hedef Rozet') }}</h3>
                @if($nextBadgeProgress)
                    <div class="flex items-start gap-4">
                        @php($nextClasses = $nextBadgeProgress['badge']->color_classes)
                        <div class="w-12 h-12 rounded-full {{ $nextClasses[0] }} flex items-center justify-center text-xl shadow {{ $nextClasses[2] }}">
                            {{ $nextBadgeProgress['badge']->icon }}
                        </div>
                        <div class="flex-1">
                            <p class="text-slate-100 font-semibold">{{ $nextBadgeProgress['badge']->name }}</p>
                            <p class="text-sm text-slate-400 mt-1">{{ $nextBadgeProgress['badge']->description }}</p>
                            <div class="mt-3 w-full bg-slate-700 rounded-full h-2.5 overflow-hidden">
                                <div class="h-2.5 bg-gradient-to-r from-emerald-500 to-cyan-500 rounded-full" style="width: {{ $nextBadgeProgress['progress'] }}%"></div>
                            </div>
                            <p class="text-xs text-slate-400 mt-2">
                                {{ __('İlerleme:') }} {{ $nextBadgeProgress['current'] }} / {{ $nextBadgeProgress['target'] }}
                                · {{ __('Kalan:') }} {{ $nextBadgeProgress['remaining'] }}
                            </p>
                        </div>
                    </div>
                @else
                    <p class="text-sm text-slate-400">{{ __('Tebrikler! Tanımlı tüm rozetleri kazandın.') }}</p>
                @endif
            </section>
        </div>
    </div>
</x-app-layout>
