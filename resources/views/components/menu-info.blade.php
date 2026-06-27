<div class="flex justify-between items-center w-full gap-3 px-3 sm:px-4 py-2">

    {{-- Logo + nombre --}}
    <a href="{{ route('dashboard') }}" class="flex items-center gap-2 min-w-0 group">
        <img src="{{ asset('images/bicicletas_logo.png') }}" alt="Bicicletería Bálsamo"
             class="h-8 w-auto shrink-0">
        <span class="hidden sm:block text-lg md:text-xl font-bold tracking-tight whitespace-nowrap">
            <span class="text-brand-600 group-hover:text-brand-700 transition-colors">Bicicletería</span>
            <span class="text-red-600">Bálsamo</span>
        </span>
    </a>

    {{-- Reloj + usuario --}}
    <div class="flex items-center gap-2 sm:gap-3 shrink-0">
        @include('components.RealTimeClock')
        @livewire('user-info')
    </div>

</div>
