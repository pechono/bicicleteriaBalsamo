@if(auth()->check() && auth()->user()->user_type === 'Admin')
    {{ $slot }}
@endif
