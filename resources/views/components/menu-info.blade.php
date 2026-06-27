<div class="flex justify-between items-center w-full gap-3 px-3 sm:px-4 py-2">

    {{-- Logo (ya incluye el nombre) --}}
    <a href="{{ route('dashboard') }}" class="flex items-center min-w-0 shrink">
        <img src="{{ asset('images/logo-balsamo.png') }}" alt="Bicicletería Bálsamo"
             class="h-10 md:h-12 w-auto shrink-0">
    </a>

    {{-- Reloj + usuario --}}
    <div class="flex items-center gap-2 sm:gap-3 shrink-0">
        @include('components.RealTimeClock')
        @livewire('user-info')
    </div>

</div>
