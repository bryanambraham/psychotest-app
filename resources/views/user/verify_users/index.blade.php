@extends('layouts.app')

@section('content')
<div class="container py-4">

    {{-- Alert Notifikasi Sukses/Error --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert" style="border-radius: 8px;">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert" style="border-radius: 8px;">
            <i class="fas fa-exclamation-triangle mr-2"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    {{-- Header Section --}}
    <div class="d-flex justify-content-between align-items-center flex-wrap mb-4 pb-2 border-bottom">
        <div>
            <h2 class="font-weight-bold text-dark mb-1" style="letter-spacing: 0.5px;">Data Verifikasi Peserta</h2>
            <p class="text-muted small mb-0">Kelola data seluruh peserta ujian yang telah memverifikasi identitasnya.</p>
        </div>
        <div class="d-flex align-items-center mt-3 mt-md-0" style="gap: 15px;">
            <span class="badge badge-primary px-3 py-2 font-weight-bold shadow-sm" style="font-size: 0.85rem; border-radius: 20px;">
                Total: {{ $users->total() }} Peserta
            </span>
        </div>
    </div>

    @if (count($users) >= 1)
        <form action="{{ route('verify_user_all.destroy') }}" method="POST" class="my-4" id="delete-form-deleteAll">
            @csrf
            @method('DELETE')
            <button type="button" class="btn btn-danger btn-sm shadow-sm btn-delete" title="Hapus Semua Verifikasi Data" style="border-radius: 6px;" id="deleteAll">
                <!-- <i class="fas fa-trash-alt"></i> -->
                    Hapus Semua Verifikasi User
            </button>
        </form>    
    @endif


    {{-- Tabel Card --}}
    <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px; overflow: hidden;">
        <div class="table-responsive">
            <table class="table table-hover table-striped text-left mb-0" style="font-size: 0.9rem;">
                <thead class="bg-light text-secondary font-weight-bold" style="font-size: 0.8rem; letter-spacing: 0.5px; text-transform: uppercase;">
                    <tr>
                        <th class="py-3 px-4" style="width: 60px;">No</th>
                        <th class="py-3 px-4">Nama Lengkap</th>
                        <th class="py-3 px-4">Email</th>
                        <th class="py-3 px-4">Posisi / Jabatan</th>
                        <th class="py-3 px-4">Telepon</th>
                        <th class="py-3 px-4 text-center" style="width: 140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-dark">
                    @forelse($users as $index => $user)
                        <tr>
                            {{-- Nomor Urut Dinamis --}}
                            <td class="align-middle px-4 font-weight-bold text-muted">
                                {{ $users->firstItem() + $index }}
                            </td>
                            
                            {{-- Nama --}}
                            <td class="align-middle px-4">
                                <div class="font-weight-bold text-dark text-capitalize">{{ $user->name }}</div>
                            </td>
                            
                            {{-- Email --}}
                            <td class="align-middle px-4">
                                <a href="mailto:{{ $user->email }}" class="text-primary" style="text-decoration: none;">{{ $user->email }}</a>
                            </td>
                            
                            {{-- Posisi --}}
                            <td class="align-middle px-4 text-capitalize">
                                @if($user->position)
                                    <span class="badge badge-light border px-2 py-1">{{ $user->position }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            
                            {{-- Telepon --}}
                            <td class="align-middle px-4">
                                {{ $user->phone ?? '-' }}
                            </td>
                            
                            {{-- Tombol Aksi --}}
                            <td class="align-middle px-4 text-center">
                                <div class="d-flex justify-content-center" style="gap: 8px;">
                                    {{-- Tombol Hapus --}}
                                    <form action="{{ route('verify_user.destroy', $user->id) }}" method="POST" class="d-inline" id="delete-form-{{ $user->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-danger btn-sm shadow-sm btn-delete" data-id="{{ $user->id }}" title="Hapus Data" style="border-radius: 6px;">
                                            <!-- <i class="fas fa-trash-alt"></i> -->
                                             Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="text-muted py-4">
                                    <i class="fas fa-users-slash fa-3x mb-3 text-muted" style="opacity: 0.4;"></i>
                                    <p class="mb-0 font-weight-bold">Belum ada data peserta yang terverifikasi.</p>
                                    <p class="small">Data akan otomatis bertambah ketika ada peserta yang melakukan verifikasi.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination Bawaan Laravel Bootstrap 4 --}}
    <div class="d-flex justify-content-center my-4">
        {{ $users->links() }}
    </div>

</div>

{{-- Script SweetAlert untuk Konfirmasi Delete yang Elegan --}}
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const deleteButtons = document.querySelectorAll('.btn-delete');
        const deleteAllButtons = document.getElementById('deleteAll')

        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const userId = this.getAttribute('data-id');
                
                Swal.fire({
                    title: 'Hapus Peserta?',
                    text: "Data verifikasi ini akan dihapus secara permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('delete-form-' + userId).submit();
                    }
                });
            });
        });

        deleteAllButtons.addEventListener('click', function() {
            Swal.fire({
                title: 'Hapus Semua Verifikasi Peserta?',
                text: "Data verifikasi ini akan dihapus secara permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-deleteAll').submit();
                }
            });
        });
    });
</script>
@endsection