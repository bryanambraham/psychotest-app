@extends('layouts.app')

@section('content')
<div class="container-fluid px-3 px-md-4">

    {{-- ===== PAGE HEADER ===== --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <h4 class="mb-0 fs-responsive font-weight-bold">Manajemen User</h4>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#importModal">
                ⬇️ Import CSV
            </button>
            <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm">+ Tambah User</a>
        </div>
    </div>

    {{-- ===== ALERTS ===== --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- ===== SEARCH BAR ===== --}}
    <x-search-bar placeholder="Masukkan nama, email, atau posisi..." tableId="usersTable" />

    {{-- ===== PAGINATION TOP ===== --}}
    <div class="my-3">
        {{ $users->links() }}
    </div>

    {{-- ===== TABLE ===== --}}
    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle" id="usersTable">
                <thead class="bg-light">
                    <tr>
                        <th class="text-nowrap">Nama</th>
                        <th class="text-nowrap d-none d-sm-table-cell">Email</th>
                        <th class="text-nowrap d-none d-md-table-cell">Posisi</th>
                        <th class="text-nowrap d-none d-md-table-cell">Phone</th>
                        <th class="text-nowrap">Password</th>
                        <th class="text-nowrap">Role</th>
                        <th class="text-nowrap text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $user->name }}</div>
                            {{-- Email & posisi tampil di bawah nama pada layar xs --}}
                            <div class="d-block d-sm-none text-muted small">{{ $user->email }}</div>
                            <div class="d-block d-md-none text-muted small">Posisi: {{ $user->position ?? '-' }}</div>
                            <div class="d-block d-md-none text-muted small">Phone: {{ $user->phone ?? '-' }}</div>
                        </td>
                        <td class="d-none d-sm-table-cell text-break" style="max-width: 180px;">
                            {{ $user->email }}
                        </td>
                        <td class="d-none d-sm-table-cell text-break" style="max-width: 180px;">{{ $user->position ?? '-' }}</td>
                        <td class="d-none d-sm-table-cell text-break" style="max-width: 180px;">{{ $user->phone ?? '-' }}</td>
                        <td class="text-break" style="max-width: 180px;">
                            @php
                                try{
                                    $decrypted_password = \Illuminate\Support\Facades\Crypt::decryptString($user->password);
                                }catch(\Illuminate\Contracts\Encryption\DecryptException $e){
                                    $decrypted_password = 'Gagal dekripsi';
                                }
                            @endphp
                            {{ $decrypted_password }}
                        </td>
                        <td>
                            <span class="badge {{ $user->role == 'admin' ? 'badge-danger' : 'badge-secondary' }}">
                                {{ strtoupper($user->role) }}
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="d-flex flex-wrap justify-content-center gap-1">
                                <a href="{{ route('users.edit', $user->id) }}"
                                   class="btn btn-sm btn-warning text-nowrap">Edit</a>
                                <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger text-nowrap"
                                            onclick="return confirm('Hapus user ini?')">Hapus</button>
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
        {{ $users->links() }}
    </div>
</div>

{{-- ===== MODAL IMPORT CSV ===== --}}
<div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importModalLabel">Import Data User</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('users.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="file">Pilih File (.csv)</label>
                        <input type="file" name="file" id="file" class="form-control" accept=".csv" required>
                    </div>
                    <div class="alert alert-info small">
                        <strong>Format CSV yang didukung:</strong><br>
                        Pastikan file CSV memiliki urutan kolom (Header opsional tetapi urutan baris data harus mengikuti ini): <br>
                        <code>Name | Email | Position | Phone | Password | Role</code>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Import Sekarang</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ===== RESPONSIVE STYLES ===== --}}
<style>
    .fs-responsive {
        font-size: clamp(1.05rem, 3.5vw, 1.35rem);
    }

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