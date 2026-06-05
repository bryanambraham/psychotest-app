@extends('layouts.app')

@section('content')
<div class="container-fluid px-3 px-md-4">

    {{-- ===== PAGE HEADER ===== --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
        <h3 class="mb-0 fs-responsive">Daftar Materi Psikotes</h3>
        <a href="{{ route('manage-exams.create') }}" class="btn btn-primary btn-sm">+ Tambah Ujian</a>
    </div>

    {{-- ===== ALERT ===== --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- ===== SEARCH BAR ===== --}}
    <x-search-bar placeholder="Masukkan nama ujian..." tableId="examsTable" />

    {{-- ===== PAGINATION TOP ===== --}}
    <div class="my-3">
        {{ $exams->links() }}
    </div>

    {{-- ===== TABLE ===== --}}
    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle" id="examsTable">
                <thead class="bg-light">
                    <tr>
                        <th class="text-nowrap">Nama Ujian</th>
                        <th class="text-nowrap d-none d-sm-table-cell">Tipe</th>
                        <th class="text-nowrap d-none d-md-table-cell">Durasi</th>
                        <th class="text-center text-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($exams as $exam)
                    <tr>
                        <td>
                            <div class="fw-bold">{{ $exam->name }}</div>
                            {{-- Tipe & durasi muncul di bawah nama di layar xs --}}
                            <div class="d-flex flex-wrap gap-1 mt-1 d-sm-none">
                                <span class="badge badge-info">{{ strtoupper($exam->type) }}</span>
                                <small class="text-muted">{{ $exam->duration_minutes }} Menit</small>
                            </div>
                        </td>
                        <td class="d-none d-sm-table-cell">
                            <span class="badge badge-info">{{ strtoupper($exam->type) }}</span>
                        </td>
                        <td class="d-none d-md-table-cell text-nowrap">
                            {{ $exam->duration_minutes }} Menit
                        </td>
                        <td class="text-center">
                            <div class="d-flex flex-wrap justify-content-center gap-1">
                                <a href="{{ route('exam.show', $exam) }}"
                                   class="btn btn-sm btn-outline-success text-nowrap">Preview</a>

                                <a href="{{ route('manage-exams.edit', $exam->id) }}"
                                   class="btn btn-sm btn-primary text-nowrap">Kelola</a>

                                <a href="{{ route('manage-exams.edit-answer-keys', $exam->id) }}"
                                   class="btn btn-sm btn-info text-nowrap" title="Edit Kunci Jawaban">
                                    <span class="d-none d-lg-inline">Kunci Jawaban</span>
                                    <span class="d-inline d-lg-none" title="Kunci Jawaban">Kunci</span>
                                </a>

                                <a href="{{ route('manage-exams.qr', $exam->id) }}"
                                   class="btn btn-sm btn-outline-secondary text-nowrap" title="Download QR">QR</a>

                                <button onclick="navigator.clipboard.writeText('{{ route('exam.show', $exam) }}'); alert('Link berhasil disalin!')"
                                        class="btn btn-sm btn-outline-info text-nowrap">
                                    <span class="d-none d-lg-inline">Copy Link</span>
                                    <span class="d-inline d-lg-none" title="Copy Link">Copy</span>
                                </button>

                                <form action="{{ route('manage-exams.destroy', $exam->id) }}" method="POST"
                                      style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger text-nowrap"
                                            onclick="return confirm('Apakah Anda yakin ingin menghapus ujian ini?')">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- ===== PAGINATION BOTTOM ===== --}}
    <div class="my-3">
        {{ $exams->links() }}
    </div>
</div>

{{-- ===== RESPONSIVE STYLES ===== --}}
<style>
    .fs-responsive {
        font-size: clamp(1.1rem, 4vw, 1.575rem);
    }

    @media (max-width: 575.98px) {
        .table td, .table th {
            font-size: 0.78rem;
            padding: 0.4rem 0.5rem;
        }
        .btn-sm {
            font-size: 0.70rem;
            padding: 0.22rem 0.4rem;
        }
        .badge {
            font-size: 0.65rem;
        }
    }

    @media (min-width: 576px) and (max-width: 991.98px) {
        .table td, .table th {
            font-size: 0.82rem;
        }
        .btn-sm {
            font-size: 0.75rem;
        }
    }

    /* gap utility fallback untuk Bootstrap 4 */
    .gap-1 { gap: 0.25rem; }
    .gap-2 { gap: 0.5rem; }
</style>
@endsection