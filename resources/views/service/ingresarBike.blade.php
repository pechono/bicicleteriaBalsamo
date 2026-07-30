<x-app-layout>

    <div class="pt-[1cm]">
        <div class="w-11/12 mx-auto sm:px-6 lg:px-8">
            @include('components.menu-servicio')
        </div>
    </div>

    <div class="mt-2">
        <livewire:service.ingresar-bike/>
    </div>
</x-app-layout>
