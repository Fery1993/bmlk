@props(['title', 'backRoute' => null])

<div class="max-w-3xl">
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
        <div class="flex items-center gap-3 px-5 py-4 border-b border-gray-100">
            @if($backRoute)
            <a href="{{ $backRoute }}" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            @endif
            <h2 class="text-sm font-semibold text-gray-700">{{ $title }}</h2>
        </div>
        <div class="p-5">
            {{ $slot }}
        </div>
    </div>
</div>
