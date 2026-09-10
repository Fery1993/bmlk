{{-- Reusable card + table wrapper --}}
{{-- Usage: <x-table-wrapper title="..." :createRoute="route('...')"> --}}

@props(['title', 'createRoute' => null, 'createLabel' => 'Tambah Baru'])

<div class="bg-white rounded-xl border border-gray-100 shadow-sm">
    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
        <h2 class="text-sm font-semibold text-gray-700">{{ $title }}</h2>
        @if($createRoute)
        <a href="{{ $createRoute }}"
           class="inline-flex items-center gap-1.5 bg-primary-700 hover:bg-primary-800 text-white text-xs font-medium px-3 py-1.5 rounded-lg transition">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            {{ $createLabel }}
        </a>
        @endif
    </div>

    {{-- Search/filter slot --}}
    @isset($filter)
    <div class="px-5 py-3 border-b border-gray-100 bg-gray-50">
        {{ $filter }}
    </div>
    @endisset

    {{-- Table --}}
    <div class="overflow-x-auto">
        {{ $slot }}
    </div>

    {{-- Pagination slot --}}
    @isset($pagination)
    <div class="px-5 py-3 border-t border-gray-100">
        {{ $pagination }}
    </div>
    @endisset
</div>
