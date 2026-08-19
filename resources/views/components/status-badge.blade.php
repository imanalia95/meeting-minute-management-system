@props(['status'])

@php
    $labels = [
        'draft' => 'Draft',
        'final' => 'Final',
        'approved' => 'Approved',
        'belum_mula' => 'Belum Mula',
        'dalam_proses' => 'Dalam Proses',
        'selesai' => 'Selesai',
        'tertangguh' => 'Tertangguh',
    ];

    $colors = [
        'draft' => 'bg-stone-100 text-stone-600 ring-stone-300',
        'final' => 'bg-blue-50 text-blue-700 ring-blue-200',
        'approved' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
        'belum_mula' => 'bg-stone-100 text-stone-600 ring-stone-200',
        'dalam_proses' => 'bg-amber-50 text-amber-700 ring-amber-200',
        'selesai' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
        'tertangguh' => 'bg-rose-50 text-rose-700 ring-rose-200',
    ];
@endphp

<span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset {{ $colors[$status] ?? 'bg-slate-100 text-slate-600 ring-slate-200' }}">
    {{ $labels[$status] ?? $status }}
</span>
