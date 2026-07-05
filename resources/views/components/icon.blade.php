@props(['name'])

@php
$paths = [
    'dashboard' => '<rect x="3" y="10" width="4" height="11" rx="1"/><rect x="10" y="5" width="4" height="16" rx="1"/><rect x="17" y="13" width="4" height="8" rx="1"/>',
    'pond' => '<rect x="3" y="3" width="8" height="8" rx="2"/><rect x="13" y="3" width="8" height="8" rx="2"/><rect x="3" y="13" width="8" height="8" rx="2"/><rect x="13" y="13" width="8" height="8" rx="2"/>',
    'cycle' => '<path d="M20 12a8 8 0 10-2.34 5.66" stroke-linecap="round"/><path d="M20 8v4h-4" stroke-linecap="round" stroke-linejoin="round"/>',
    'feed' => '<rect x="3" y="4" width="18" height="16" rx="2"/><line x1="7" y1="9" x2="17" y2="9" stroke-linecap="round"/><line x1="7" y1="13" x2="17" y2="13" stroke-linecap="round"/><line x1="7" y1="17" x2="14" y2="17" stroke-linecap="round"/>',
    'flask' => '<path d="M9 3h6M10 3v5l-5 9a2 2 0 001.8 3h10.4a2 2 0 001.8-3l-5-9V3" stroke-linecap="round" stroke-linejoin="round"/>',
    'sampling' => '<circle cx="10" cy="10" r="6"/><line x1="14.5" y1="14.5" x2="20" y2="20" stroke-linecap="round"/>',
    'droplet' => '<path d="M12 3s6 7 6 11a6 6 0 01-12 0c0-4 6-11 6-11z" stroke-linecap="round" stroke-linejoin="round"/>',
    'harvest' => '<path d="M3 8l9-4 9 4-9 4-9-4z" stroke-linecap="round" stroke-linejoin="round"/><path d="M3 8v9l9 4 9-4V8" stroke-linecap="round" stroke-linejoin="round"/><path d="M12 12v9" stroke-linecap="round"/>',
    'alert' => '<path d="M12 3l10 18H2L12 3z" stroke-linecap="round" stroke-linejoin="round"/><line x1="12" y1="10" x2="12" y2="13.5" stroke-linecap="round"/><circle cx="12" cy="16.5" r="0.75" fill="currentColor" stroke="none"/>',
    'report' => '<rect x="2" y="6" width="20" height="12" rx="2"/><circle cx="12" cy="12" r="3"/><line x1="6" y1="9" x2="6" y2="9.01" stroke-linecap="round" stroke-width="2.2"/><line x1="18" y1="15" x2="18" y2="15.01" stroke-linecap="round" stroke-width="2.2"/>',
    'plus' => '<line x1="12" y1="5" x2="12" y2="19" stroke-linecap="round"/><line x1="5" y1="12" x2="19" y2="12" stroke-linecap="round"/>',
    'menu' => '<line x1="3" y1="6" x2="21" y2="6" stroke-linecap="round"/><line x1="3" y1="12" x2="21" y2="12" stroke-linecap="round"/><line x1="3" y1="18" x2="21" y2="18" stroke-linecap="round"/>',
    'close' => '<line x1="6" y1="6" x2="18" y2="18" stroke-linecap="round"/><line x1="18" y1="6" x2="6" y2="18" stroke-linecap="round"/>',
    'chevron-down' => '<polyline points="6 9 12 15 18 9" stroke-linecap="round" stroke-linejoin="round"/>',
    'logout' => '<path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4" stroke-linecap="round" stroke-linejoin="round"/><polyline points="16 17 21 12 16 7" stroke-linecap="round" stroke-linejoin="round"/><line x1="21" y1="12" x2="9" y2="12" stroke-linecap="round"/>',
    'user' => '<circle cx="12" cy="8" r="3.5"/><path d="M4.5 20c1.5-4 5-6 7.5-6s6 2 7.5 6" stroke-linecap="round"/>',
    'clock' => '<circle cx="12" cy="12" r="8.5"/><path d="M12 7.5V12l3 2" stroke-linecap="round" stroke-linejoin="round"/>',
    'arrow-left' => '<line x1="19" y1="12" x2="5" y2="12" stroke-linecap="round"/><polyline points="11 6 5 12 11 18" stroke-linecap="round" stroke-linejoin="round"/>',
    'arrow-right' => '<line x1="5" y1="12" x2="19" y2="12" stroke-linecap="round"/><polyline points="13 6 19 12 13 18" stroke-linecap="round" stroke-linejoin="round"/>',
    'trend-up' => '<polyline points="4 15 10 9 14 13 20 6" stroke-linecap="round" stroke-linejoin="round"/><polyline points="14 6 20 6 20 12" stroke-linecap="round" stroke-linejoin="round"/>',
    'pencil' => '<path d="M4 20l4-1 11-11a2 2 0 000-3l-1-1a2 2 0 00-3 0L4 15l-1 4z" stroke-linecap="round" stroke-linejoin="round"/>',
    'trash' => '<polyline points="4 7 20 7" stroke-linecap="round"/><path d="M6 7v13a1 1 0 001 1h10a1 1 0 001-1V7M9 7V5a1 1 0 011-1h4a1 1 0 011 1v2" stroke-linecap="round" stroke-linejoin="round"/>',
];
@endphp

<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" {{ $attributes }}>
    {!! $paths[$name] ?? '' !!}
</svg>
