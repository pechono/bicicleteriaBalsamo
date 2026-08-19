<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div id="app">
        <x-banner />

        <div class="min-h-screen bg">
            <!-- PRUEBA: menú horizontal arriba (logo + opciones con submenús flotantes).
                 Para volver al menú lateral: comentar este <header> y descomentar el bloque de abajo. -->
            <header class="sticky top-0 z-40 bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
                @include('components.menu-info')       {{-- logo + reloj + usuario --}}
                @include('components.menu-horizontal') {{-- opciones (submenús flotan, no empujan) --}}
                @include('components.quick-access')    {{-- accesos rápidos --}}
            </header>

            {{-- ===== Menú lateral anterior (dejar por si se quiere volver) =====
            <div class="flex">
                @include('components.menu-desplegable')
                <div id="main" class="flex-1 transition-all duration-500">
                    <header class="sticky top-0 z-30 bg-white shadow-sm border-b border-gray-200 pl-14 md:pl-16">
                        @include('components.menu-info')
                        @include('components.quick-access')
                    </header>
                </div>
            </div>
            ================================================================= --}}

            <main class="px-3 sm:px-4 pt-2 pb-6">
                {{ $slot }}
            </main>
        </div>
    </div>

    <script>
        function openNav() {
            document.getElementById("mySidebar").style.width = "16rem";
            // En desktop empuja el contenido; en celular queda como overlay (no aplasta)
            document.getElementById("main").style.marginLeft = window.innerWidth >= 768 ? "16rem" : "0";
        }

        function closeNav() {
            document.getElementById("mySidebar").style.width = "0";
            document.getElementById("main").style.marginLeft = "0";
        }

        function toggleSubMenu(id) {
            const subMenu = document.getElementById(id);
            if (subMenu.classList.contains('hidden')) {
                subMenu.classList.remove('hidden');
            } else {
                subMenu.classList.add('hidden');
            }
        }
    </script>
</body>
</html>
