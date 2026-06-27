<div>
    @if ($user)
        <div class="flex items-center gap-2 bg-white/15 border border-white/30 rounded-full pl-1 pr-3 py-1">
            <div class="h-7 w-7 rounded-full bg-white text-brand-700 text-xs font-bold flex items-center justify-center shrink-0">
                {{ strtoupper(mb_substr($user->name, 0, 1)) }}
            </div>
            <span class="hidden md:block text-sm font-medium text-white truncate max-w-[160px]">{{ $user->name }}</span>
        </div>
    @else
        <span class="text-sm text-white/70">Sin usuario</span>
    @endif
</div>
