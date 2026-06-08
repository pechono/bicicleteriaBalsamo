<x-app-layout>
    <div class="pt-20">
        <div class="w-11/12 mx-auto sm:px-6 lg:px-8">
            @include('components.menu-stock')
        </div>
    </div>

    <div class="mt-2">
        <div class="w-full px-2 sm:px-4">
            <livewire:stock.pedido-livewire/>
        </div>
    </div>
</x-app-layout>
