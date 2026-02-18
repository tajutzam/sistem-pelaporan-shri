@props(['href', 'icon' => ''])

@php
    $isActive = request()->is(ltrim($href, '/') . '*');
@endphp

<a href="{{ $href }}"
    class="flex items-center gap-3 p-3 rounded-lg transition-all duration-200 {{ $isActive ? 'bg-white text-[#A80532] shadow-md' : 'hover:bg-white/10 hover:translate-x-1' }}">
    <span>{{ $icon }}</span>
    <span class="truncate">{{ $slot }}</span>
</a>
