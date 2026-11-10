@extends('layouts.app')

@section('content')
<div class="container-fluid px-3 px-md-4">

    {{-- ===== PAGE HEADER ===== --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
        <div>
            <h3 class="mb-0 fs-responsive font-weight-bold">Manajemen Role &amp; Hak Akses (Permission)</h3>
            <p class="text-muted small mb-0">Atur hak akses setiap role untuk mengizinkan atau membatasi tombol Create, Read, Update, Delete (CRUD).</p>
        </div>
        @can('roles.create')
            <a href="{{ route('roles.create') }}" class="btn btn-primary btn-sm shadow-sm font-weight-bold">
                <i class="fas fa-plus-circle mr-1"></i> Tambah Role Baru
            </a>
        @endcan
    </div>

    {{-- ===== ALERTS ===== --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    {{-- ===== TABLE CARD ===== --}}
    <div class="card shadow-sm border-0" style="border-radius: 12px; overflow: hidden;">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="rolesTable">
                <thead class="bg-light text-secondary">
                    <tr>
                        <th class="py-3 px-4" style="width: 250px;">Role</th>
                        <th class="py-3 px-4">Deskripsi</th>
                        <th class="py-3 px-4 text-center" style="width: 140px;">Jumlah User</th>
                        <th class="py-3 px-4 text-center" style="width: 180px;">Hak Akses</th>
                        <th class="py-3 px-4 text-center" style="width: 160px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($roles as $role)
                        <tr>
                            <td class="px-4">
                                <div class="font-weight-bold text-dark fs-6">{{ $role->display_name }}</div>
                                <code class="small text-muted">{{ $role->name }}</code>
                            </td>
                            <td class="px-4 text-muted small">
                                {{ $role->description ?? '-' }}
                            </td>
                            <td class="px-4 text-center">
                                <span class="badge badge-pill badge-info px-3 py-2 font-weight-bold">
                                    <i class="fas fa-users mr-1"></i> {{ $role->users_count }} User
                                </span>
                            </td>
                            <td class="px-4 text-center">
                                @if($role->name === 'admin')
                                    <span class="badge badge-pill badge-success px-3 py-2 font-weight-bold">
                                        <i class="fas fa-shield-alt mr-1"></i> Akses Penuh (All)
                                    </span>
                                @else
                                    <span class="badge badge-pill badge-primary px-3 py-2 font-weight-bold">
                                        {{ $role->permissions_count }} Izin / Permission
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    @can('roles.edit')
                                        <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-sm btn-warning text-nowrap" title="Edit Role & Permission">
                                            <i class="fas fa-key mr-1"></i> Atur Akses
                                        </a>
                                    @endcan

                                    @can('roles.delete')
                                        @if(!in_array($role->name, ['admin', 'user']))
                                            <form action="{{ route('roles.destroy', $role->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus role {{ $role->display_name }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger text-nowrap" {{ $role->users_count > 0 ? 'disabled title="Masih ada user"' : '' }}>
                                                    <i class="fas fa-trash-alt"></i> Hapus
                                                </button>
                                            </form>
                                        @endif
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">Belum ada role yang dibuat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .gap-1 { gap: 0.25rem; }
    .gap-2 { gap: 0.5rem; }
</style>
@endsection
