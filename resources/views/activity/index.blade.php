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

        /* USER */
        .user-info {
            display: flex;
            align-items: center;
            gap: .75rem;
        }

        .user-avatar {

            width: 38px;
            height: 38px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #2563eb;
            color: #fff;
            font-weight: 700;
            font-size: .9rem;
        }

        .user-detail strong {
            display: block;
            color: #0f172a;
        }

        .user-detail small {
            color: #64748b;
        }



        /* TABLE */
        .modern-table {
            vertical-align: middle;
        }

        .modern-table tbody tr {
            transition: .2s;
        }


        .modern-table tbody tr:hover {
            background: #f8fafc;
        }


        /* BADGE */
        .event-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }



        /* EMPTY */
        .empty-state {
            padding: 3rem;
        }

        /* PAGINATION */
        .pagination {
            gap: .35rem;
            margin-bottom: 0;
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

        /* Active */
        .pagination .page-item.active .page-link {

            background: #2563eb;

            border-color: #2563eb;

            color: white;

        }

        /* Hover */

        .pagination .page-link:hover {

            background: #eff6ff;

            color: #2563eb;

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
                <form method="GET">
                    <div class="filter-box mb-4">
                        <div class="row g-3">
                            <div class="col-md-2">
                                <label class="form-label">
                                    Event
                                </label>
                                <select name="event" class="form-select">
                                    <option value="">
                                        Semua Event
                                    </option>
                                    @foreach (['create' => 'CREATE', 'update' => 'UPDATE', 'delete' => 'DELETE', 'login' => 'LOGIN', 'logout' => 'LOGOUT'] as $key => $label)
                                        <option value="{{ $key }}" @selected(request('event') == $key)>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">
                                    User
                                </label>
                                <select name="causer_id" class="form-select">
                                    <option value="">
                                        Semua User
                                    </option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}" @selected(request('causer_id') == $user->id)>
                                            {{ $user->nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">
                                    Cari Aktivitas
                                </label>

                                <input type="text" name="search" class="form-control" value="{{ request('search') }}"
                                    placeholder="Cari aktivitas...">
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">
                                    Dari
                                </label>
                                <input type="date" name="start_date" class="form-control"
                                    value="{{ request('start_date') }}">
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">
                                    Sampai
                                </label>
                                <input type="date" name="end_date" class="form-control"
                                    value="{{ request('end_date') }}">
                            </div>

                            <div class="col-md-1 d-flex align-items-end">
                                <button class="btn btn-primary w-100">
                                    <i class="bi bi-funnel"></i>
                                </button>
                            </div>


                            <div class="col-md-1 d-flex align-items-end">
                                <a href="{{ route('activity.index') }}" class="btn btn-secondary w-100">
                                    <i class="bi bi-arrow-clockwise"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table modern-table">
                        <thead>
                            <tr>
                                <th>Waktu</th>
                                <th>User</th>
                                <th>Module</th>
                                <th>Aktivitas</th>
                                <th>Event</th>
                                <th width="80">
                                    Detail
                                </th>
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
                                    $userName = $activity->causer?->name ?? 'System';
                                @endphp

                                <tr>
                                    <td>
                                        <div>
                                            {{ $activity->created_at->format('d M Y H:i') }}
                                        </div>

                                        <small class="text-muted">
                                            {{ $activity->created_at->diffForHumans() }}
                                        </small>
                                    </td>

                                    <td>
                                        <div class="user-info">
                                            <div class="user-avatar">
                                                {{ strtoupper(substr($activity->causer?->nama, 0, 1)) }}
                                            </div>

                                            <div class="user-detail">
                                                <strong>
                                                    {{ $activity->causer?->nama ?? 'System' }}
                                                </strong>

                                                <small>
                                                    {{ $activity->causer?->email ?? '-' }}
                                                </small>
                                            </div>
                                        </div>
                                    </td>

                                    <td>
                                        <span class="badge bg-light text-dark border">
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
                                            <h6 class="mt-3">
                                                Tidak ada activity log
                                            </h6>

                                            <p class="text-muted mb-0">
                                                Coba ubah filter pencarian.
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{ $activities->onEachSide(1)->links('pagination::bootstrap-5') }}

            </div>
        </div>
    </main>
@endsection

@foreach ($activities as $activity)
    @php
        $module = $activity->subject_type ? class_basename($activity->subject_type) : '-';

        $properties = $activity->properties ?? [];

        $old = $properties['old'] ?? [];
        $new = $properties['new'] ?? [];

        $namaMadrasah = $properties['nama_madrasah'] ?? '-';
    @endphp


    <div class="modal fade" id="detail{{ $activity->id }}" tabindex="-1">

        <div class="modal-dialog modal-lg modal-dialog-scrollable">

            <div class="modal-content">


                <div class="modal-header">

                    <h5 class="modal-title">

                        <i class="bi bi-clock-history"></i>

                        Detail Activity

                    </h5>


                    <button type="button" class="btn-close" data-bs-dismiss="modal">
                    </button>

                </div>



                <div class="modal-body">


                    {{-- INFO UTAMA --}}

                    <div class="row g-3 mb-3">


                        <div class="col-md-6">

                            <label class="text-muted small">
                                User
                            </label>

                            <div class="fw-semibold">

                                {{ $activity->causer?->nama ?? 'System' }}

                            </div>

                            <div>

                                {{ $activity->causer?->email }}

                            </div>

                        </div>



                        <div class="col-md-6">

                            <label class="text-muted small">
                                Waktu
                            </label>

                            <div>

                                {{ $activity->created_at->format('d M Y H:i:s') }}

                            </div>

                        </div>



                        <div class="col-md-6">

                            <label class="text-muted small">
                                Event
                            </label>

                            <div>

                                <span class="badge bg-primary">

                                    {{ strtoupper($activity->event) }}

                                </span>

                            </div>

                        </div>



                        <div class="col-md-6">

                            <label class="text-muted small">
                                Module
                            </label>

                            <div>

                                {{ $module }}

                            </div>

                        </div>



                        <div class="col-12">

                            <label class="text-muted small">
                                Aktivitas
                            </label>


                            <p class="mb-0">

                                {{ $activity->description }}

                            </p>

                        </div>


                    </div>



                    <hr>



                    {{-- REQUEST INFO --}}


                    <h6>

                        <i class="bi bi-globe"></i>

                        Informasi Request

                    </h6>



                    <table class="table table-sm">


                        <tr>

                            <th width="150">
                                IP Address
                            </th>

                            <td>

                                {{ $properties['ip_address'] ?? '-' }}

                            </td>

                        </tr>



                        <tr>

                            <th>
                                User Agent
                            </th>

                            <td style="word-break:break-word">

                                {{ $properties['user_agent'] ?? '-' }}

                            </td>

                        </tr>


                    </table>





                    {{-- IMPORT INFO --}}


                    @if ($activity->event == 'import')
                        <hr>


                        <h6>

                            <i class="bi bi-file-earmark-arrow-up"></i>

                            Informasi Import

                        </h6>



                        <table class="table table-sm table-bordered">


                            <tr>

                                <th width="180">

                                    Jenis Prestasi

                                </th>


                                <td>

                                    {{ $properties['jenis'] ?? '-' }}

                                </td>


                            </tr>



                            <tr>

                                <th>

                                    Jumlah Data

                                </th>


                                <td>

                                    <span class="badge bg-success">

                                        {{ $properties['jumlah_data'] ?? 0 }}
                                        Data

                                    </span>

                                </td>


                            </tr>



                            <tr>

                                <th>
                                    Madrasah
                                </th>

                                <td>
                                    {{ $namaMadrasah }}
                                </td>

                            </tr>



                        </table>
                    @endif






                    {{-- UPDATE / CREATE --}}


                    @if ($activity->event != 'import' && (count($old) || count($new)))
                        <hr>


                        <h6>

                            <i class="bi bi-pencil-square"></i>

                            Perubahan Data

                        </h6>



                        <table class="table table-bordered table-sm">


                            <thead class="table-light">

                                <tr>

                                    <th>
                                        Field
                                    </th>

                                    <th>
                                        Sebelum
                                    </th>

                                    <th>
                                        Sesudah
                                    </th>

                                </tr>

                            </thead>



                            <tbody>


                                @foreach ($new as $field => $value)
                                    <tr>


                                        <td class="fw-semibold">

                                            {{ ucwords(str_replace('_', ' ', $field)) }}

                                        </td>



                                        <td class="text-danger">


                                            {{ $old[$field] ?? '-' }}


                                        </td>



                                        <td class="text-success">


                                            {{ is_array($value) ? json_encode($value) : $value }}


                                        </td>


                                    </tr>
                                @endforeach


                            </tbody>


                        </table>
                    @endif







                    {{-- DELETE --}}


                    @if (isset($properties['deleted_data']))
                        <hr class="my-2">


                        <h6 class="text-danger mb-2">


                            <i class="bi bi-trash"></i>


                            Deleted Data


                        </h6>



                        <div class="table-responsive">


                            <table class="table table-sm table-bordered mb-0">


                                <tbody>


                                    @foreach ($properties['deleted_data'] as $field => $value)
                                        <tr>


                                            <th class="bg-light text-muted fw-semibold py-1 px-2">


                                                {{ ucwords(str_replace('_', ' ', $field)) }}


                                            </th>



                                            <td class="py-1 px-2">


                                                @if (is_array($value))
                                                    <small>

                                                        {{ json_encode($value) }}

                                                    </small>
                                                @else
                                                    <small>

                                                        {{ $value ?? '-' }}

                                                    </small>
                                                @endif


                                            </td>


                                        </tr>
                                    @endforeach


                                </tbody>


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
