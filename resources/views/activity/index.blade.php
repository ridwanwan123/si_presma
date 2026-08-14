@extends('layouts.base')

@push('styles')
    <style>
        .page-title {
            padding: 0 1rem;
            margin-bottom: 1rem;
        }

        .page-title h2 {
            font-size: 1.8rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: .25rem;
        }

        .page-title p {
            color: #64748b;
            margin: 0;
        }

        /* CARD */
        .content-card {
            margin: 0 1rem 1rem;
            background: #fff;
            border-radius: 18px;
            border: 1px solid #e5e7eb;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, .05);
        }

        .content-card-body {
            padding: 1.25rem;
        }

        /* FILTER */
        .filter-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 1rem;
            border-radius: 14px;
        }

        .filter-box .form-label {
            font-size: .8rem;
            font-weight: 600;
            color: #475569;
            margin-bottom: .35rem;
        }

        .filter-box .form-control,
        .filter-box .form-select {
            border-radius: 10px;
        }

        .filter-box .form-control:focus,
        .filter-box .form-select:focus {
            border-color: #0f8a43;
            box-shadow: 0 0 0 .15rem rgba(15, 138, 67, .15);
        }

        /* USER */
        .user-info {
            display: flex;
            align-items: center;
            gap: .65rem;
        }

        .user-avatar {
            flex-shrink: 0;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #2563eb;
            color: #fff;
            font-weight: 700;
            font-size: .85rem;
        }

        .user-detail strong {
            display: block;
            color: #0f172a;
            font-size: .85rem;
        }

        .user-detail small {
            color: #94a3b8;
            font-size: .74rem;
        }

        /* =========================
           TABLE (COMPACT + SCROLL)
        ========================= */

        .table-scroll {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            border: 1px solid #eef2f7;
            border-radius: 12px;
        }

        .modern-table {
            min-width: 900px;
            margin-bottom: 0;
            vertical-align: middle;
            font-size: .85rem;
        }

        .modern-table thead th {
            position: sticky;
            top: 0;
            white-space: nowrap;
            background: #f8fafc;
            color: #475569;
            font-weight: 700;
            font-size: .78rem;
            letter-spacing: .03em;
            text-transform: uppercase;
            border-bottom: 1px solid #e2e8f0;
            padding: .75rem .9rem;
        }

        .modern-table tbody td {
            padding: .65rem .9rem;
        }

        .modern-table tbody tr:not(:last-child) td {
            border-bottom: 1px solid #f1f5f9;
        }

        .modern-table tbody tr {
            transition: .15s;
        }

        .modern-table tbody tr:hover {
            background: #f8fffb;
        }

        /* kolom "Waktu" pinned supaya tetap terlihat saat scroll horizontal */
        .modern-table thead th:first-child,
        .modern-table tbody td:first-child {
            position: sticky;
            left: 0;
            background: #fff;
            z-index: 1;
        }

        .modern-table thead th:first-child {
            background: #f8fafc;
            z-index: 2;
        }

        .modern-table tbody tr:hover td:first-child {
            background: #f8fffb;
        }

        .table-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            flex-wrap: wrap;
            margin-top: 1rem;
        }

        .table-footer .result-count {
            color: #64748b;
            font-size: .82rem;
        }

        /* BADGE */
        .event-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            white-space: nowrap;
        }

        .module-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            white-space: nowrap;
        }

        /* EMPTY */
        .empty-state {
            padding: 3rem;
        }

        /* PAGINATION */
        .pagination {
            gap: .35rem;
            margin-bottom: 0;
            flex-wrap: wrap;
        }

        .pagination .page-item .page-link {
            border-radius: 8px;
            min-width: 38px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 .75rem;
            font-size: .875rem;
            color: #475569;
            border: 1px solid #e2e8f0;
            background: #fff;
        }

        .pagination .page-item:first-child .page-link,
        .pagination .page-item:last-child .page-link {
            padding-left: 1rem;
            padding-right: 1rem;
        }

        .pagination .page-item.active .page-link {
            background: #2563eb;
            border-color: #2563eb;
            color: white;
        }

        .pagination .page-link:hover {
            background: #eff6ff;
            color: #2563eb;
        }

        @media(max-width:768px) {
            .content-card {
                margin: 0 .5rem 1rem;
            }

            .content-card-body {
                padding: 1rem;
            }
        }

        /* =========================================================
           MODAL DETAIL ACTIVITY
           Menampilkan SEMUA atribut bawaan Spatie Activitylog
           (log_name, event, subject, causer, batch_uuid, properties)
           dengan tata letak yang mudah dibaca, bukan raw JSON dump.
        ========================================================= */

        .activity-modal .modal-header {
            background: linear-gradient(135deg, #0f172a, #1e293b);
            color: #fff;
            border-bottom: none;
            padding: 1.25rem 1.5rem;
        }

        .activity-modal .modal-header .modal-title {
            font-size: 1.05rem;
            font-weight: 700;
        }

        .activity-modal .modal-header small {
            color: #94a3b8;
        }

        .activity-modal .modal-body {
            padding: 1.5rem;
        }

        /* meta grid: pasangan label/value ringkas di bagian atas modal */
        .meta-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem 1.5rem;
            margin-bottom: 1.25rem;
        }

        @media(max-width:576px) {
            .meta-grid {
                grid-template-columns: 1fr;
            }
        }

        .meta-item .meta-label {
            display: flex;
            align-items: center;
            gap: .4rem;
            font-size: .72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .04em;
            color: #94a3b8;
            margin-bottom: .3rem;
        }

        .meta-item .meta-value {
            font-size: .9rem;
            color: #0f172a;
            font-weight: 600;
            word-break: break-word;
        }

        .meta-item .meta-value small {
            display: block;
            font-weight: 400;
            color: #64748b;
            font-size: .78rem;
            margin-top: .1rem;
        }

        .desc-box {
            background: #f8fafc;
            border: 1px solid #eef2f7;
            border-radius: 12px;
            padding: .9rem 1rem;
            font-size: .88rem;
            color: #334155;
            margin-bottom: 1.25rem;
        }

        .detail-section {
            margin-bottom: 1.25rem;
        }

        .detail-section:last-child {
            margin-bottom: 0;
        }

        .detail-section-title {
            display: flex;
            align-items: center;
            gap: .5rem;
            font-size: .85rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: .65rem;
        }

        .detail-section-title i {
            color: #2563eb;
        }

        /* key-value table generik, dipakai untuk info teknis & properti lain */
        .kv-table {
            width: 100%;
            font-size: .84rem;
            border: 1px solid #eef2f7;
            border-radius: 10px;
            overflow: hidden;
        }

        .kv-table tr:not(:last-child) th,
        .kv-table tr:not(:last-child) td {
            border-bottom: 1px solid #f1f5f9;
        }

        .kv-table th {
            width: 180px;
            background: #f8fafc;
            color: #64748b;
            font-weight: 600;
            padding: .6rem .85rem;
            vertical-align: top;
        }

        .kv-table td {
            padding: .6rem .85rem;
            color: #0f172a;
            word-break: break-word;
        }

        .diff-table {
            width: 100%;
            font-size: .84rem;
            border: 1px solid #eef2f7;
            border-radius: 10px;
            overflow: hidden;
        }

        .diff-table thead th {
            background: #f8fafc;
            color: #475569;
            font-weight: 700;
            font-size: .74rem;
            text-transform: uppercase;
            letter-spacing: .03em;
            padding: .55rem .8rem;
        }

        .diff-table tbody td {
            padding: .55rem .8rem;
            border-top: 1px solid #f1f5f9;
            vertical-align: top;
            word-break: break-word;
        }

        .diff-table tbody td.field-name {
            font-weight: 600;
            color: #0f172a;
            width: 180px;
        }

        .diff-table tbody td.value-old {
            color: #dc2626;
            background: #fef2f2;
        }

        .diff-table tbody td.value-new {
            color: #0f8a43;
            background: #f0fdf4;
        }

        .batch-pill {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            background: #eff6ff;
            color: #2563eb;
            border-radius: 999px;
            padding: .3rem .7rem;
            font-size: .75rem;
            font-weight: 600;
            font-family: monospace;
        }
    </style>
@endpush

@section('content')
    <main class="content">
        <div class="page-title">
            <h2>
                Activity Log
            </h2>
            <p>
                Riwayat aktivitas pengguna dalam sistem.
            </p>
        </div>

        <div class="content-card">
            <div class="content-card-body">

                {{-- ================= FILTER ================= --}}
                <form method="GET">
                    <div class="filter-box mb-4">
                        <div class="row g-3">
                            <div class="col-6 col-md-2">
                                <label class="form-label">Event</label>
                                <select name="event" class="form-select">
                                    <option value="">Semua Event</option>
                                    @foreach (['create' => 'CREATE', 'update' => 'UPDATE', 'delete' => 'DELETE', 'login' => 'LOGIN', 'logout' => 'LOGOUT'] as $key => $label)
                                        <option value="{{ $key }}" @selected(request('event') == $key)>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-6 col-md-2">
                                <label class="form-label">User</label>
                                <select name="causer_id" class="form-select">
                                    <option value="">Semua User</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}" @selected(request('causer_id') == $user->id)>
                                            {{ $user->nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12 col-md-3">
                                <label class="form-label">Cari Aktivitas</label>
                                <input type="text" name="search" class="form-control" value="{{ request('search') }}"
                                    placeholder="Cari aktivitas...">
                            </div>

                            <div class="col-6 col-md-2">
                                <label class="form-label">Dari</label>
                                <input type="date" name="start_date" class="form-control"
                                    value="{{ request('start_date') }}">
                            </div>

                            <div class="col-6 col-md-2">
                                <label class="form-label">Sampai</label>
                                <input type="date" name="end_date" class="form-control"
                                    value="{{ request('end_date') }}">
                            </div>

                            <div class="col-6 col-md-auto d-flex align-items-end">
                                <button class="btn btn-primary w-100">
                                    <i class="bi bi-funnel"></i>
                                    Filter
                                </button>
                            </div>

                            <div class="col-6 col-md-auto d-flex align-items-end">
                                <a href="{{ route('activity.index') }}" class="btn btn-secondary w-100">
                                    <i class="bi bi-arrow-clockwise"></i>
                                    Reset
                                </a>
                            </div>
                        </div>
                    </div>
                </form>

                {{-- ================= TABLE ================= --}}
                <div class="table-scroll">
                    <table class="table modern-table">
                        <thead>
                            <tr>
                                <th>Waktu</th>
                                <th>User</th>
                                <th>Module</th>
                                <th>Aktivitas</th>
                                <th>Event</th>
                                <th width="70"></th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($activities as $activity)
                                @php
                                    $module = $activity->subject_type ? class_basename($activity->subject_type) : '-';
                                    $event = strtolower($activity->event);
                                    $eventData = match ($event) {
                                        'created', 'create' => [
                                            'color' => 'success',
                                            'icon' => 'bi-plus-circle',
                                            'label' => 'CREATE',
                                        ],
                                        'updated', 'update' => [
                                            'color' => 'primary',
                                            'icon' => 'bi-pencil-square',
                                            'label' => 'UPDATE',
                                        ],
                                        'deleted', 'delete' => [
                                            'color' => 'danger',
                                            'icon' => 'bi-trash',
                                            'label' => 'DELETE',
                                        ],
                                        'login' => [
                                            'color' => 'success',
                                            'icon' => 'bi-box-arrow-in-right',
                                            'label' => 'LOGIN',
                                        ],
                                        'logout' => [
                                            'color' => 'secondary',
                                            'icon' => 'bi-box-arrow-left',
                                            'label' => 'LOGOUT',
                                        ],
                                        default => [
                                            'color' => 'dark',
                                            'icon' => 'bi-info-circle',
                                            'label' => strtoupper($activity->event),
                                        ],
                                    };
                                @endphp

                                <tr>
                                    <td>
                                        <div>{{ $activity->created_at->format('d M Y H:i') }}</div>
                                        <small class="text-muted">{{ $activity->created_at->diffForHumans() }}</small>
                                    </td>

                                    <td>
                                        <div class="user-info">
                                            <div class="user-avatar">
                                                {{ strtoupper(substr($activity->causer?->nama ?? 'S', 0, 1)) }}
                                            </div>
                                            <div class="user-detail">
                                                <strong>{{ $activity->causer?->nama ?? 'System' }}</strong>
                                                <small>{{ $activity->causer?->email ?? '-' }}</small>
                                            </div>
                                        </div>
                                    </td>

                                    <td>
                                        <span class="badge bg-light text-dark border module-badge">
                                            <i class="bi bi-box"></i>
                                            {{ $module }}
                                        </span>
                                    </td>

                                    <td>
                                        <div style="max-width:300px">
                                            {{ $activity->description }}
                                        </div>
                                    </td>

                                    <td>
                                        <span class="badge bg-{{ $eventData['color'] }} event-badge">
                                            <i class="bi {{ $eventData['icon'] }}"></i>
                                            {{ $eventData['label'] }}
                                        </span>
                                    </td>

                                    <td>
                                        <button class="btn btn-sm btn-light border" data-bs-toggle="modal"
                                            data-bs-target="#detail{{ $activity->id }}">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6">
                                        <div class="empty-state text-center">
                                            <i class="bi bi-journal-x fs-1 text-muted"></i>
                                            <h6 class="mt-3">Tidak ada activity log</h6>
                                            <p class="text-muted mb-0">Coba ubah filter pencarian.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="table-footer">
                    <span class="result-count">
                        Menampilkan {{ $activities->firstItem() ?? 0 }}–{{ $activities->lastItem() ?? 0 }}
                        dari {{ $activities->total() }} aktivitas
                    </span>

                    {{ $activities->onEachSide(1)->links('pagination::bootstrap-5') }}
                </div>

            </div>
        </div>
    </main>
@endsection

{{-- =========================================================
     MODAL DETAIL ACTIVITY
     Semua atribut bawaan Spatie Activitylog (log_name, event,
     subject_type/id, causer_type/id, batch_uuid, properties)
     ditampilkan di sini dengan format yang mudah dipahami.
========================================================= --}}
@foreach ($activities as $activity)
    @php
        $module = $activity->subject_type ? class_basename($activity->subject_type) : '-';
        $causerType = $activity->causer_type ? class_basename($activity->causer_type) : null;

        $event = strtolower($activity->event);
        $eventData = match ($event) {
            'created', 'create' => ['color' => 'success', 'icon' => 'bi-plus-circle', 'label' => 'CREATE'],
            'updated', 'update' => ['color' => 'primary', 'icon' => 'bi-pencil-square', 'label' => 'UPDATE'],
            'deleted', 'delete' => ['color' => 'danger', 'icon' => 'bi-trash', 'label' => 'DELETE'],
            'login' => ['color' => 'success', 'icon' => 'bi-box-arrow-in-right', 'label' => 'LOGIN'],
            'logout' => ['color' => 'secondary', 'icon' => 'bi-box-arrow-left', 'label' => 'LOGOUT'],
            default => ['color' => 'dark', 'icon' => 'bi-info-circle', 'label' => strtoupper($activity->event)],
        };

        $properties = $activity->properties ? $activity->properties->toArray() : [];

        $old = $properties['old'] ?? [];
        $new = $properties['new'] ?? [];
        $namaMadrasah = $properties['nama_madrasah'] ?? null;

        // key yang sudah ditampilkan secara khusus di section lain,
        // supaya tidak dobel muncul di "Data Tambahan".
        $handledKeys = ['old', 'new', 'deleted_data', 'ip_address', 'user_agent', 'jenis', 'jumlah_data', 'nama_madrasah'];
        $extraProperties = collect($properties)->except($handledKeys)->filter(fn($v) => $v !== null && $v !== '');
    @endphp

    <div class="modal fade" id="detail{{ $activity->id }}" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content activity-modal">

                {{-- HEADER --}}
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title">
                            <i class="bi bi-clock-history"></i>
                            Detail Activity Log
                        </h5>
                        <small>#{{ $activity->id }} &middot; {{ $activity->log_name ?? 'default' }}</small>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    {{-- ================= RINGKASAN ================= --}}
                    <div class="meta-grid">
                        <div class="meta-item">
                            <div class="meta-label"><i class="bi bi-person"></i> User</div>
                            <div class="meta-value">
                                {{ $activity->causer?->nama ?? 'System' }}
                                @if ($activity->causer?->email)
                                    <small>{{ $activity->causer->email }}</small>
                                @elseif($causerType)
                                    <small>Tipe: {{ $causerType }}</small>
                                @endif
                            </div>
                        </div>

                        <div class="meta-item">
                            <div class="meta-label"><i class="bi bi-clock"></i> Waktu</div>
                            <div class="meta-value">
                                {{ $activity->created_at->format('d M Y, H:i:s') }}
                                <small>{{ $activity->created_at->diffForHumans() }}</small>
                            </div>
                        </div>

                        <div class="meta-item">
                            <div class="meta-label"><i class="bi bi-lightning"></i> Event</div>
                            <div class="meta-value">
                                <span class="badge bg-{{ $eventData['color'] }} event-badge">
                                    <i class="bi {{ $eventData['icon'] }}"></i>
                                    {{ $eventData['label'] }}
                                </span>
                            </div>
                        </div>

                        <div class="meta-item">
                            <div class="meta-label"><i class="bi bi-box"></i> Module / Subject</div>
                            <div class="meta-value">
                                {{ $module }}
                                @if ($activity->subject_id)
                                    <small>ID: {{ $activity->subject_id }}</small>
                                @endif
                            </div>
                        </div>

                        @if ($activity->batch_uuid)
                            <div class="meta-item">
                                <div class="meta-label"><i class="bi bi-stack"></i> Batch</div>
                                <div class="meta-value">
                                    <span class="batch-pill">
                                        <i class="bi bi-hash"></i>
                                        {{ \Illuminate\Support\Str::limit($activity->batch_uuid, 18) }}
                                    </span>
                                    <small>Aktivitas ini bagian dari proses/batch yang sama.</small>
                                </div>
                            </div>
                        @endif

                        @if ($namaMadrasah)
                            <div class="meta-item">
                                <div class="meta-label"><i class="bi bi-building"></i> Madrasah Terkait</div>
                                <div class="meta-value">{{ $namaMadrasah }}</div>
                            </div>
                        @endif
                    </div>

                    <div class="desc-box">
                        {{ $activity->description }}
                    </div>

                    {{-- ================= PERUBAHAN DATA (UPDATE/CREATE) ================= --}}
                    @if (count($old) || count($new))
                        <div class="detail-section">
                            <div class="detail-section-title">
                                <i class="bi bi-pencil-square"></i>
                                Perubahan Data
                            </div>

                            <div class="table-responsive">
                                <table class="diff-table">
                                    <thead>
                                        <tr>
                                            <th>Field</th>
                                            <th>Sebelum</th>
                                            <th>Sesudah</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($new as $field => $value)
                                            <tr>
                                                <td class="field-name">
                                                    {{ ucwords(str_replace('_', ' ', $field)) }}
                                                </td>
                                                <td class="value-old">
                                                    {{ is_array($old[$field] ?? null) ? json_encode($old[$field]) : ($old[$field] ?? '-') }}
                                                </td>
                                                <td class="value-new">
                                                    {{ is_array($value) ? json_encode($value) : $value }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    {{-- ================= IMPORT INFO ================= --}}
                    @if ($event == 'import')
                        <div class="detail-section">
                            <div class="detail-section-title">
                                <i class="bi bi-file-earmark-arrow-up"></i>
                                Informasi Import
                            </div>

                            <table class="kv-table">
                                <tr>
                                    <th>Jenis Prestasi</th>
                                    <td>{{ $properties['jenis'] ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Jumlah Data</th>
                                    <td>
                                        <span class="badge bg-success">
                                            {{ $properties['jumlah_data'] ?? 0 }} Data
                                        </span>
                                    </td>
                                </tr>
                                @if ($namaMadrasah)
                                    <tr>
                                        <th>Madrasah</th>
                                        <td>{{ $namaMadrasah }}</td>
                                    </tr>
                                @endif
                            </table>
                        </div>
                    @endif

                    {{-- ================= DATA YANG DIHAPUS ================= --}}
                    @if (isset($properties['deleted_data']))
                        <div class="detail-section">
                            <div class="detail-section-title text-danger">
                                <i class="bi bi-trash"></i>
                                Data yang Dihapus
                            </div>

                            <table class="kv-table">
                                @foreach ($properties['deleted_data'] as $field => $value)
                                    <tr>
                                        <th>{{ ucwords(str_replace('_', ' ', $field)) }}</th>
                                        <td>
                                            @if (is_array($value))
                                                {{ json_encode($value) }}
                                            @else
                                                {{ $value ?? '-' }}
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                    @endif

                    {{-- ================= DATA TAMBAHAN (properties lain yang belum tercover) ================= --}}
                    @if ($extraProperties->isNotEmpty())
                        <div class="detail-section">
                            <div class="detail-section-title">
                                <i class="bi bi-plus-square"></i>
                                Data Tambahan
                            </div>

                            <table class="kv-table">
                                @foreach ($extraProperties as $key => $value)
                                    <tr>
                                        <th>{{ ucwords(str_replace('_', ' ', $key)) }}</th>
                                        <td>
                                            @if (is_array($value))
                                                {{ json_encode($value) }}
                                            @else
                                                {{ $value }}
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                    @endif

                    {{-- ================= INFORMASI TEKNIS (IP & User Agent) ================= --}}
                    @if (!empty($properties['ip_address']) || !empty($properties['user_agent']))
                        <div class="detail-section">
                            <div class="detail-section-title">
                                <i class="bi bi-globe"></i>
                                Informasi Teknis
                            </div>

                            <table class="kv-table">
                                <tr>
                                    <th><i class="bi bi-geo-alt me-1"></i> IP Address</th>
                                    <td>{{ $properties['ip_address'] ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th><i class="bi bi-laptop me-1"></i> User Agent</th>
                                    <td style="word-break:break-word">{{ $properties['user_agent'] ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    @endif

                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
@endforeach