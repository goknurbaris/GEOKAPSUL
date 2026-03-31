<!DOCTYPE html>
<html lang="tr" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Çok Fazla İstek | GeoKapsül</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .glass-card {
            background: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(16px) saturate(180%);
            border: 1px solid rgba(239, 68, 68, 0.3);
        }
    </style>
</head>
<body class="bg-slate-900 min-h-screen flex items-center justify-center p-4">
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-red-500/10 rounded-full blur-3xl"></div>
        <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-orange-500/10 rounded-full blur-3xl"></div>
    </div>

    <div class="relative z-10 w-full max-w-md text-center">
        <div class="glass-card rounded-2xl p-8">
            <div class="w-20 h-20 mx-auto mb-6 rounded-full bg-red-500/20 flex items-center justify-center">
                <span class="text-5xl">🚫</span>
            </div>

            <h1 class="text-2xl font-bold text-white mb-2">Yavaşla Biraz!</h1>
            <p class="text-slate-400 mb-6">Çok fazla istek gönderdin. Lütfen biraz bekle ve tekrar dene.</p>

            <div class="bg-slate-800/50 rounded-lg p-4 mb-6">
                <p class="text-sm text-slate-500">
                    Güvenlik için istek sayısını sınırlıyoruz.
                    <br>Bir dakika sonra tekrar deneyebilirsin.
                </p>
            </div>

            <a href="{{ url()->previous() }}"
               class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-500 hover:to-blue-500 text-white font-semibold rounded-lg transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Geri Dön
            </a>
        </div>
    </div>
</body>
</html>
