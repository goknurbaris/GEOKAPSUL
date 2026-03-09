<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-2xl text-slate-800 tracking-tight uppercase">
                Kapsül Arşivim
            </h2>
            <div class="flex items-center gap-3">
                <span class="hidden md:block bg-indigo-100 text-indigo-700 px-4 py-1.5 rounded-full text-xs font-black uppercase tracking-widest">
                    {{ $myCapsules->count() }} Kapsül
                </span>
                <a href="{{ url('/') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl text-xs font-black transition-all shadow-md uppercase tracking-widest">
                    + Yeni Ekle
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if($myCapsules->isEmpty())
                <div class="bg-white border border-slate-200 rounded-[3rem] p-20 text-center shadow-sm">
                    <div class="text-6xl mb-6">📍</div>
                    <h3 class="text-3xl font-black text-slate-800 mb-2 uppercase tracking-tight">Henüz Kapsülün Yok</h3>
                    <p class="text-slate-500 mb-8 font-medium">Haritaya git ve ilk dijital izini bırak.</p>
                    <a href="{{ url('/') }}" class="inline-flex px-10 py-4 bg-indigo-600 text-white font-black rounded-2xl shadow-lg hover:bg-indigo-700 transition-all uppercase tracking-widest">Haritayı Aç</a>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($myCapsules as $capsule)
                        <div class="bg-white border-2 border-slate-100 rounded-[2.5rem] p-6 shadow-md hover:shadow-xl transition-all duration-300 flex flex-col justify-between group">

                            <div>
                                <div class="flex justify-between items-start mb-6">
                                    <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center border border-indigo-100 shadow-inner group-hover:rotate-6 transition-transform">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                        </svg>
                                    </div>

                                    <div class="flex gap-2">
                                        <button onclick="editCapsule({{ $capsule->id }}, '{{ addslashes($capsule->message) }}')" class="w-10 h-10 flex items-center justify-center text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-xl transition-colors border border-transparent hover:border-indigo-100" title="Düzenle">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 00 2 2h14a2 2 0 00 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                        </button>

                                        <form action="{{ route('capsule.destroy', $capsule) }}" method="POST" onsubmit="return confirm('Sonsuza dek silinecek! Emin misin?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="w-10 h-10 flex items-center justify-center text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition-colors border border-transparent hover:border-rose-100" title="Sil">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"></path></svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                @if($capsule->image)
                                    <div class="mb-5 rounded-2xl overflow-hidden border-2 border-slate-100 shadow-sm relative group-hover:border-indigo-100 transition-colors">
                                        <img src="{{ asset('storage/' . $capsule->image) }}" alt="Kapsül Anısı" class="w-full h-48 object-cover transform hover:scale-110 transition-transform duration-700">
                                    </div>
                                @endif

                                <div class="mb-6 bg-slate-50 p-4 rounded-2xl border border-slate-100">
                                    <p class="text-lg text-slate-800 font-bold italic leading-snug">
                                        "{{ $capsule->message }}"
                                    </p>
                                </div>
                            </div>

                            <div class="pt-5 border-t-2 border-slate-100 space-y-2">
                                <p class="text-[11px] font-black text-indigo-500 uppercase tracking-widest flex items-center">
                                    <span class="w-2 h-2 bg-indigo-500 rounded-full mr-2"></span>
                                    Kordinat: {{ round($capsule->latitude, 4) }}, {{ round($capsule->longitude, 4) }}
                                </p>
                                <p class="text-[11px] font-black text-slate-400 uppercase tracking-widest flex items-center">
                                    <span class="w-2 h-2 bg-slate-300 rounded-full mr-2"></span>
                                    Tarih: {{ $capsule->created_at->format('d/m/Y H:i') }}
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
