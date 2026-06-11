<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Bicicletería Balsamo — App</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background: linear-gradient(135deg, #064e3b 0%, #065f46 50%, #047857 100%); min-height: 100vh; }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-4">

    <div class="w-full max-w-md">

        {{-- Logo / Cabecera --}}
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-white/20 rounded-2xl mb-4 shadow-xl">
                <span class="text-4xl">🚲</span>
            </div>
            <h1 class="text-3xl font-extrabold text-white">Bicicletería Balsamo</h1>
            <p class="text-emerald-200 mt-1">App para mecánicos</p>
        </div>

        {{-- Card principal --}}
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">

            {{-- Banner --}}
            <div class="bg-gradient-to-r from-emerald-600 to-emerald-700 px-6 py-4">
                <p class="text-white font-semibold text-center text-sm">
                    📱 Disponible para Android
                </p>
            </div>

            <div class="p-6 space-y-6">

                {{-- Características --}}
                <div class="space-y-3">
                    @foreach([
                        ['🔍', 'Escaneá QR de artículos para ver precios'],
                        ['🚲', 'Consultá órdenes de servicio desde el taller'],
                        ['🔧', 'Agregá artículos a las bicis en reparación'],
                        ['✅', 'Los admins pueden marcar trabajos como terminados'],
                    ] as [$icon, $texto])
                    <div class="flex items-center gap-3">
                        <span class="text-xl w-8 text-center flex-shrink-0">{{ $icon }}</span>
                        <span class="text-gray-600 text-sm">{{ $texto }}</span>
                    </div>
                    @endforeach
                </div>

                <hr class="border-gray-100" />

                {{-- Botón de descarga --}}
                <a href="https://app.bicicleteriabalsamo.xyz/downloads/bicicleteria.apk"
                    class="flex items-center justify-center gap-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-4 px-6 rounded-xl transition shadow-lg w-full text-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    <div class="text-left">
                        <div>Descargar APK</div>
                        <div class="text-xs text-emerald-200 font-normal">~97 MB · Android</div>
                    </div>
                </a>

                {{-- Instrucciones de instalación --}}
                <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
                    <p class="text-amber-800 font-semibold text-sm mb-2">⚠️ Antes de instalar</p>
                    <ol class="text-amber-700 text-xs space-y-1 list-decimal list-inside">
                        <li>Descargá el APK desde el botón de arriba</li>
                        <li>Abrí <strong>Ajustes → Seguridad</strong> en tu Android</li>
                        <li>Activá <strong>"Instalar apps de fuentes desconocidas"</strong></li>
                        <li>Abrí el archivo descargado e instalá</li>
                        <li>Iniciá sesión con tu usuario del sistema</li>
                    </ol>
                </div>

                {{-- Soporte --}}
                <p class="text-center text-gray-400 text-xs">
                    ¿Problemas? Contactá al administrador del sistema.
                </p>

            </div>
        </div>

        {{-- Footer --}}
        <p class="text-center text-emerald-300 text-xs mt-6">
            Bicicletería Balsamo © {{ date('Y') }}
        </p>

    </div>

</body>
</html>
