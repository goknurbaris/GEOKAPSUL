<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-2xl text-slate-800 tracking-tight">
                Kapsül Arşivim
            </h2>
            <div class="flex items-center gap-3">
                <span class="hidden md:block bg-indigo-100 text-indigo-700 px-3 py-1 rounded-full text-xs font-bold">
                    {{ $myCapsules->count() }} Kapsül
                </span>
                <a href="{{ url('/') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-xl text-sm font-bold transition-all shadow-md">
                    + Yeni Ekle
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if($myCapsules->isEmpty())
                <div class="bg-white border border-slate-200 rounded-3xl p-16 text-center shadow-sm">
                    <div class="text-6xl mb-4">📍</div>
                    <h3 class="text-2xl font-bold text-slate-800 mb-2">Henüz Hiç Kapsülün Yok</h3>
                    <p class="text-slate-500 mb-6">Haritaya git ve ilk dijital izini bırak.</p>
                    <a href="{{ url('/') }}" class="inline-flex px-8 py-3 bg-indigo-600 text-white font-bold rounded-xl shadow-md hover:bg-indigo-700 transition-all">Haritayı Aç</a>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($myCapsules as $capsule)
                        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow flex flex-col justify-between">

                            <div>
                                <div class="flex justify-between items-start mb-4">
                                    <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center border border-indigo-100">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                        </svg>
                                    </div>

                                    <div class="flex gap-1">
                                        <button onclick="editCapsule({{ $capsule->id }}, '{{ addslashes($capsule->message) }}')" class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors" title="Düzenle">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 00 2 2h14a2 2 0 00 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                        </button>

                                        <form action="{{ route('capsule.destroy', $capsule) }}" method="POST" onsubmit="return confirm('Silmek istediğine emin misin?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Sil">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"></path></svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                <div class="mb-6">
                                    <p class="text-lg text-slate-800 font-medium italic">
                                        "{{ $capsule->message }}"
                                    </p>
                                </div>
                            </div>

                            <div class="pt-4 border-t border-slate-100 space-y-2">
                                <p class="text-xs font-semibold text-slate-500 flex items-center">
                                    <span class="w-1.5 h-1.5 bg-indigo-500 rounded-full mr-2"></span>
                                    Konum: {{ round($capsule->latitude, 4) }}, {{ round($capsule->longitude, 4) }}
                                </p>
                                <p class="text-xs font-semibold text-slate-400 flex items-center">
                                    <span class="w-1.5 h-1.5 bg-slate-300 rounded-full mr-2"></span>
                                    Tarih: {{ $capsule->created_at->format('d.m.Y H:i') }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

        </div>
    </div>

    <script>
        function editCapsule(id, oldMessage) {
            const newMessage = prompt("Mesajı Güncelle:", oldMessage);
            if (newMessage !== null && newMessage !== oldMessage && newMessage.trim() !== "") {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/kapsul/${id}`;
                form.innerHTML = `
                    @csrf @method('PATCH')
                    <input type="hidden" name="message" value="${newMessage}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</x-app-layout>
