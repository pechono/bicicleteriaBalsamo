<div>
    @if ($user)
        <div class="flex items-center gap-2 bg-brand-50 border border-brand-200 rounded-full pl-1 pr-3 py-1">
            <div class="h-7 w-7 rounded-full bg-brand-600 text-white text-xs font-bold flex items-center justify-center shrink-0">
                {{ strtoupper(mb_substr($user->name, 0, 1)) }}
            </div>
            <span class="hidden md:block text-sm font-medium text-brand-800 truncate max-w-[160px]">{{ $user->name }}</span>
        </div>
    @else
        <span class="text-sm text-gray-400">Sin usuario</span>
    @endif
</div>
