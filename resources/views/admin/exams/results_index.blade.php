@extends('layouts.app')

@section('content')
<div class="container-fluid px-3 px-md-4">

    {{-- ===== PAGE HEADER ===== --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
        <h3 class="mb-0 fs-responsive">Laporan Hasil Ujian Peserta</h3>
    </div>

    {{-- ===== SEARCH BAR ===== --}}
    <x-search-bar placeholder="Masukkan nama, email, atau posisi..." tableId="examUsersTable" />

    {{-- ===== PAGINATION TOP ===== --}}
    <div class="my-3">
        {{ $sessions->links() }}
    </div>

    {{-- ===== TABLE ===== --}}
    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle" id="examUsersTable">
                <thead class="bg-light">
                    <tr>
                        <th class="text-nowrap">Nama Peserta</th>
                        <th class="text-nowrap">Email</th>
                        <th class="text-nowrap d-none d-md-table-cell">Posisi</th>
                        <th class="text-nowrap d-none d-lg-table-cell">No. Telepon</th>
                        <th class="text-nowrap">Ujian</th>
                        <th class="text-nowrap">Status</th>
                        <th class="text-nowrap d-none d-sm-table-cell">Nilai</th>
                        <th class="text-nowrap d-none d-sm-table-cell">Waktu Mulai</th>
                        <th class="text-nowrap text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sessions as $session)
                    <tr>
                        <td class="text-nowrap">{{ $session->user->name }}</td>
                        <td class="text-break" style="max-width: 160px;">{{ $session->user->email }}</td>
                        <td class="d-none d-md-table-cell">{{ $session->user->position ?? '-' }}</td>
                        <td class="text-nowrap d-none d-lg-table-cell">{{ $session->user->phone ?? '-' }}</td>
                        <td>{{ $session->exam->name }}</td>
                        <td>
                            <span class="badge {{ $session->status == 'completed' ? 'badge-success' : 'badge-warning' }}">
                                {{ strtoupper($session->status) }}
                            </span>
                        </td>
                        <td class="text-nowrap d-none d-sm-table-cell">
                            @if($session->score !== null)
                                <span class="badge badge-lg" style="
                                    @if($session->score >= 80)
                                        background-color: #28a745;
                                    @elseif($session->score >= 60)
                                        background-color: #ffc107;
                                        color: #000;
                                    @else
                                        background-color: #dc3545;
                                    @endif
                                ">
                                    {{ number_format($session->score, 2) }}
                                </span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-nowrap d-none d-sm-table-cell">
                            {{ $session->created_at->format('d M Y, H:i') }}
                        </td>
                        <td class="text-center d-flex justify-content-center gap-2">
                            <a href="{{ route('manage-exams.results.show', $session->id) }}"
                               class="btn btn-sm btn-info text-white text-nowrap">
                                <span class="d-none d-md-inline">Lihat Detail &amp; Foto</span>
                                <span class="d-inline d-md-none">Detail</span>
                            </a>

                            <form action="{{ route('manage-exams.results.destroy', $session->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger text-white text-nowrap" onclick="return confirm('Apakah Anda yakin ingin menghapus hasil ujian ini?')">
                                    <span class="d-none d-md-inline">Hapus</span>
                                    <span class="d-inline d-md-none">Hapus</span>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- ===== PAGINATION BOTTOM ===== --}}
    <div class="my-3">
        {{ $sessions->links() }}
    </div>
</div>

{{-- ===== RESPONSIVE STYLES ===== --}}
<style>
    /* Fluid font for page title */
    .fs-responsive {
        font-size: clamp(1.1rem, 4vw, 1.575rem);
    }

    /* Tighten table on mobile */
    @media (max-width: 575.98px) {
        .table td, .table th {
            font-size: 0.78rem;
            padding: 0.4rem 0.5rem;
        }
        .btn-sm {
            font-size: 0.72rem;
            padding: 0.25rem 0.45rem;
        }
        .badge {
            font-size: 0.65rem;
        }
    }

    /* Slightly more breathing room on tablets */
    @media (min-width: 576px) and (max-width: 991.98px) {
        .table td, .table th {
            font-size: 0.82rem;
        }
    }

    /* gap utility fallback for older Bootstrap 4 */
    .gap-1 { gap: 0.25rem; }
    .gap-2 { gap: 0.5rem; }
</style>
@endsection