<div class="flex justify-between items-center w-full px-4 py-2">

    <!-- Logo + Nombre -->
    <div class="flex items-center gap-3">
        <img
            src="{{ asset('images/bicicletas_logo.png') }}"
            alt="Bicicletería Bálsamo"
            class="h-10 w-auto"
        >
<span
  class="text-2xl font-semibold tracking-wide
         text-green-500
         hover:text-blue-500
         transition-colors duration-300"
>
  Bicicletería Bálsamo
</span>
        {{-- <span class="text-2xl font-semibold tracking-wide">
            Bicicletería Bálsamo
        </span> --}}
    </div>

    <!-- Usuario + Reloj -->
    <div class="flex items-center gap-4">
        @livewire('user-info')
        @include('components.RealTimeClock')
    </div>

</div>


