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

        <div class="min-h-screen flex bg">
            <!-- Sidebar -->
            @include('components.menu-desplegable')

            <!-- Page Content -->
            <div id="main" class="flex-1 transition-all duration-500">
                <header class="sticky top-0 z-30 bg-brand-700 shadow-md pl-14 md:pl-16">
                    @include('components.menu-info')
                </header>

                <main class="p-4">
                    {{ $slot }}
                </main>
            </div>
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
