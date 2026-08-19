
Pdf.blade · PHP
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; color: #2d2d2d; }
        h1 { font-size: 18px; margin-bottom: 2px; }
        .subtitle { color: #666; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        td, th { padding: 4px 6px; vertical-align: top; text-align: left; }
        .details-table td:first-child { font-weight: bold; width: 150px; color: #444; }
        .section-title { font-size: 14px; font-weight: bold; margin-top: 22px; margin-bottom: 8px;
                          border-bottom: 1px solid #999; padding-bottom: 4px; }
        .attendance-table th { background-color: #e8ece5; border-bottom: 1px solid #ccc; }
        .attendance-table td { border-bottom: 1px solid #eee; }
        .agenda-block { margin-bottom: 14px; padding: 10px; border: 1px solid #ddd; border-radius: 4px; }
        .agenda-title { font-weight: bold; margin-bottom: 6px; }
        .field-label { font-size: 10px; color: #777; text-transform: uppercase; margin-bottom: 2px; }
        .field-value { margin-bottom: 8px; }
        .action-table th { background-color: #e8ece5; border-bottom: 1px solid #ccc; }
        .action-table td { border-bottom: 1px solid #eee; }
        .status-pill { padding: 2px 8px; border-radius: 8px; font-size: 10px; }
        .footer-note { margin-top: 30px; font-size: 10px; color: #999; text-align: center; }
    </style>
</head>
<body>
 
    <h1>{{ $meeting->title }}</h1>
    <p class="subtitle">As Salam Meeting Minutes Management System</p>
 
    <div class="section-title">Butiran Mesyuarat</div>
    <table class="details-table">
        <tr><td>Tarikh</td><td>{{ $meeting->meeting_date->format('d F Y') }}</td></tr>
        <tr><td>Masa</td><td>{{ $meeting->start_time }}@if($meeting->end_time) - {{ $meeting->end_time }}@endif</td></tr>
        <tr><td>Lokasi</td><td>{{ $meeting->location }}</td></tr>
        <tr><td>Pengerusi</td><td>{{ $meeting->chairperson->name }}</td></tr>
        <tr><td>Setiausaha</td><td>{{ $meeting->secretary->name }}</td></tr>
        <tr><td>Status</td><td>{{ ucfirst($meeting->status) }}</td></tr>
    </table>
 
    <div class="section-title">Kehadiran</div>
    <table class="attendance-table">
        <thead>
            <tr><th>Nama</th><th>Jawatan</th><th>Status</th></tr>
        </thead>
        <tbody>
            @foreach ($meeting->attendees as $attendee)
                <tr>
                    <td>{{ $attendee->name }}</td>
                    <td>{{ $attendee->position ?? '—' }}</td>
                    <td>{{ str_replace('_', ' ', ucfirst($attendee->pivot->attendance_status)) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
 
    <div class="section-title">Agenda &amp; Perbincangan</div>
    @forelse ($meeting->agendaItems as $index => $item)
        <div class="agenda-block">
            <div class="agenda-title">{{ $index + 1 }}. {{ $item->title }}</div>
            <div class="field-label">Ringkasan Perbincangan</div>
            <div class="field-value">{{ $item->discussion->summary ?: '—' }}</div>
            <div class="field-label">Keputusan / Fokus</div>
            <div class="field-value">{{ $item->discussion->decision ?: '—' }}</div>
        </div>
    @empty
        <p>Tiada agenda direkodkan.</p>
    @endforelse
 
    <div class="section-title">Tindakan Susulan</div>
    <table class="action-table">
        <thead>
            <tr><th>Tindakan</th><th>Ditugaskan Kepada</th><th>Tarikh Akhir</th><th>Status</th></tr>
        </thead>
        <tbody>
            @forelse ($meeting->actionItems as $action)
                <tr>
                    <td>{{ $action->description }}</td>
                    <td>{{ $action->assignee->name }}</td>
                    <td>{{ optional($action->due_date)->format('d/m/Y') ?? '—' }}</td>
                    <td>{{ str_replace('_', ' ', ucfirst($action->status)) }}</td>
                </tr>
            @empty
                <tr><td colspan="4">Tiada tindakan direkodkan.</td></tr>
            @endforelse
        </tbody>
    </table>
 
    <div class="section-title">Penutup</div>
    <table class="details-table">
        <tr><td>Mesyuarat Akan Datang</td><td>{{ optional($meeting->next_meeting_date)->format('d F Y') ?? 'Belum ditetapkan' }}</td></tr>
    </table>
 
    <p class="footer-note">Dijana pada {{ now()->format('d F Y, H:i') }} melalui As Salam Meeting Minutes Management System</p>
 
</body>
</html>
 
