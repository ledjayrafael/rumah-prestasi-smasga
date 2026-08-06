@props(['status'])

@php
    $colors = match ($status->value) {
        'pending' => 'bg-amber-100 text-amber-700',
        'approved' => 'bg-green-100 text-green-700',
        'revision' => 'bg-red-100 text-red-700',
    };
@endphp
<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 text-[11px] font-bold px-2.5 py-1 rounded-full $colors"]) }}>
    {{ $status->label() }}
</span>
