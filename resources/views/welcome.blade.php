<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <title>Bicicleteria Balsamo</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  
     <style>
        .carousel-container {
            overflow: hidden; /* Oculta el contenido desbordado */
            position: relative; /* Para posicionar los botones correctamente */
        }
        .carousel-inner {
            display: flex;
            gap:0.1rem; /* Espacio entre las tarjetas */
            transition: transform 0.3s ease-in-out;
            will-change: transform; /* Mejora de rendimiento */
        }
        .carousel-item {
            flex: 0 0 calc(100% / 5 - 0.5rem); /* Mostrar 3 tarjetas */
        }
       
       

    </style>
</head>
<body class="bg-gray-100 text-gray-800">

    <!-- Header -->
   <header class="bg-white shadow-md">
    <div class="container mx-auto flex justify-between items-center py-4 px-6">
       <a href="/" class="flex items-center">
            <div class="bg-indigo-700 rounded-xl px-4 py-2 shadow">
                <img src="{{ asset('images/logo-balsamo.png') }}" alt="Bicicletería Balsamo" class="h-10 md:h-12 w-auto">
            </div>
        </a>
        
        <nav class="hidden md:flex space-x-4">
            <a href="#" class="text-gray-700 hover:text-indigo-600 transition duration-700">Inicio</a>
            <a href="#features" class="text-gray-700 hover:text-indigo-600 transition duration-700" >Características</a>
            <a href="#testimonials" class="text-gray-700 hover:text-indigo-600 transition duration-700">Testimonios</a>
            <a href="#contact" class="text-gray-700 hover:text-indigo-600 transition duration-700">Contacto</a>
        </nav>
        <div class="hidden md:flex space-x-4">
            @if (Route::has('login'))
                @auth
                    <a href="{{ url('/dashboard') }}" class="rounded-md px-3 py-2 text-black bg-gray-200 hover:bg-gray-300 transition duration-300">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="rounded-md px-3 py-2 text-black bg-gray-200 hover:bg-gray-300 transition duration-300">Ingresar</a>
                    {{--  --}}
                @endauth
            @endif
        </div>
        <!-- Botón de menú para pantallas pequeñas -->
        <div class="md:hidden">
            <button id="menu-button" class="text-gray-700 hover:text-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
                </svg>
            </button>
        </div>
    </div>
    <!-- Menú desplegable para pantallas pequeñas -->
    <div id="mobile-menu" class="hidden md:hidden">
        <nav class="flex flex-col space-y-2 py-2 px-6 bg-white border-t border-gray-200">
            <a href="#" class="text-gray-700 hover:text-indigo-600 transition duration-300">Inicio</a>
            <a href="#features" class="text-gray-700 hover:text-indigo-600 transition duration-700">Características</a>
            <a href="#testimonials" class="text-gray-700 hover:text-indigo-600 transition duration-300">Testimonios</a>
            <a href="#contact" class="text-gray-700 hover:text-indigo-600 transition duration-300">Contacto</a>
            @if (Route::has('login'))
                @auth
                    <a href="{{ url('/dashboard') }}" class="rounded-md px-3 py-2 text-black bg-gray-200 hover:bg-gray-300 transition duration-300">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="rounded-md px-3 py-2 text-black bg-gray-200 hover:bg-gray-300 transition duration-300">Log in</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="rounded-md px-3 py-2 text-black bg-gray-200 hover:bg-gray-300 transition duration-300">Register</a>
                    @endif
                @endauth
            @endif
        </nav>
    </div>
</header>

<script>
    // Script para el botón de menú
    const menuButton = document.getElementById('menu-button');
    const mobileMenu = document.getElementById('mobile-menu');

    menuButton.addEventListener('click', () => {
        mobileMenu.classList.toggle('hidden');
    });
</script>

    <!-- Hero Section -->
    <section class="bg-indigo-600 text-white py-20 text-center">
        <h1 class="text-4xl md:text-5xl font-bold">Tu bicicletería de confianza</h1>
        <p class="mt-4 text-lg md:text-xl">Venta de bicicletas, repuestos, accesorios y taller especializado.</p>
        <a href="{{ route('login') }}" class="mt-6 inline-block bg-white text-indigo-600 font-bold py-3 px-6 rounded-full hover:bg-gray-200">Ingresar al sistema</a>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-16 bg-gray-100">
        <div class="container mx-auto px-6 text-center">
            <h2 class="text-3xl font-bold">Qué ofrecemos</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-12">
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="text-4xl mb-3">🚲</div>
                    <h3 class="text-xl font-bold">Venta de bicicletas</h3>
                    <p class="mt-4 text-gray-600">Rodados infantiles, MTB, ruta y paseo. Las mejores marcas y modelos.</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="text-4xl mb-3">🔧</div>
                    <h3 class="text-xl font-bold">Taller y service</h3>
                    <p class="mt-4 text-gray-600">Mantenimiento, armado y reparación con mecánicos especializados.</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="text-4xl mb-3">⚙️</div>
                    <h3 class="text-xl font-bold">Repuestos y accesorios</h3>
                    <p class="mt-4 text-gray-600">Cubiertas, cámaras, cascos, luces y todo lo que tu bici necesita.</p>
                </div>
            </div>
        </div>
    </section>
    
    {{-- <section id="features" class="py-16 bg-gray-100">
        @livewire('imagenes.mostrar-imagenes')
    </section> --}}
  
    <!-- Testimonials Section -->
    <section id="testimonials" class="py-16 bg-white">
        <div class="container mx-auto px-6 text-center">
            <h2 class="text-3xl font-bold">Lo que dicen nuestros clientes</h2>
            <div class="mt-12 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="p-6 rounded-lg shadow-lg bg-gray-100">
                    <p class="text-gray-700">"Me armaron la bici para competir y quedó impecable. Excelente atención."</p>
                    <span class="block mt-4 font-bold text-indigo-600">Martín G.</span>
                </div>
                <div class="p-6 rounded-lg shadow-lg bg-gray-100">
                    <p class="text-gray-700">"Service rápido y a buen precio. Mi bici quedó como nueva."</p>
                    <span class="block mt-4 font-bold text-indigo-600">Carla R.</span>
                </div>
                <div class="p-6 rounded-lg shadow-lg bg-gray-100">
                    <p class="text-gray-700">"Gran variedad de repuestos y asesoramiento de verdad. Recomendados."</p>
                    <span class="block mt-4 font-bold text-indigo-600">Diego P.</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="py-16 bg-indigo-600 text-white">
        <div class="container mx-auto px-6 text-center">
            <h2 class="text-3xl font-bold">Mantenete al día</h2>
            <p class="mt-4">Dejanos tu correo y recibí novedades, ofertas y promos de la bicicletería.</p>
            <form class="mt-8 flex justify-center">
                <input type="email" placeholder="Tu correo electrónico" class="w-full max-w-md py-3 px-4 rounded-l-lg text-gray-700">
                <button type="submit" class="bg-white text-indigo-600 font-bold py-3 px-6 rounded-r-lg hover:bg-gray-200">Enviar</button>
            </form>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-400 py-6">
        <div class="container mx-auto px-6 text-center">
            <p>&copy; {{ date('Y') }} Bicicletería Balsamo. Todos los derechos reservados.</p>
            <nav class="mt-4">
                <a href="#" class="text-gray-400 hover:text-white mx-2">Privacidad</a>
                <a href="#" class="text-gray-400 hover:text-white mx-2">Términos</a>
                <a href="#" class="text-gray-400 hover:text-white mx-2">Contacto</a>
            </nav>
        </div>
    </footer>

</body>
</html>

