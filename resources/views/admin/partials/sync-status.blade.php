@php
    $tone = match ($status) {
        'success' => 'emerald',
        'failed' => 'red',
        'running' => 'cyan',
        'pending' => 'slate',
        default => 'slate',
    };
@endphp
<x-ui.badge :tone="$tone">{{ $status }}</x-ui.badge>