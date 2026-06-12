@extends('layouts.app')

@section('content')
<div class="container-fluid px-3 px-md-4 py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-sm-11 col-lg-8">

            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0 pl-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="card shadow-sm border-0">
                <div class="card-body p-3 p-md-5">

                    {{-- Header --}}
                    <div class="mb-4">
                        <span class="badge badge-info mb-2">PSIKOTEST</span>
                        <h3 class="font-weight-bold mb-2 fs-responsive">{{ $exam->name }}</h3>
                        <p class="text-muted mb-0 small">Isi data peserta terlebih dahulu. Timer baru dimulai setelah Anda menekan tombol mulai pada halaman soal.</p>
                    </div>

                    {{-- Info Cards --}}
                    <div class="row mb-4">
                        <div class="col-6 col-md-4 mb-3 mb-md-0">
                            <div class="p-3 border rounded h-100 bg-light">
                                <div class="small text-muted">Durasi</div>
                                <div class="h5 mb-0">{{ $exam->duration_minutes }} menit</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-6 mb-3 mb-md-0">
                            <div class="p-3 border rounded h-100 bg-light">
                                <div class="small text-muted">Tipe</div>
                                {{-- <div class="h5 mb-0 text-uppercase">{{ $exam->type }}</div> --}}
                            </div>
                        </div>
                    </div>

                    @if(!empty($exam->description))
                        <label class="font-weight-bold">Deskripsi</label>
                        <div class="alert alert-light border">
                            {{ $exam->description }}
                        </div>
                    @endif

                    {{-- Form --}}
                    <form method="POST" action="{{ route('exam.participant.store', $exam) }}">
                        @csrf
                        <div class="form-row">
                            <div class="form-group col-12 col-md-6">
                                <label class="font-weight-bold">Nama Lengkap</label>
                                <input type="text" name="name" class="form-control form-control-lg"
                                       value="{{ old('name', $participant['name'] ?? '') }}"
                                       placeholder="Nama peserta" required>
                            </div>
                            <div class="form-group col-12 col-md-6">
                                <label class="font-weight-bold">Posisi</label>
                                <input type="text" name="position" class="form-control form-control-lg"
                                       value="{{ old('position', $participant['position'] ?? '') }}"
                                       placeholder="Posisi yang diinginkan" required>
                            </div>
                            <div class="form-group col-12 col-md-6">
                                <label class="font-weight-bold">Nomor Telepon</label>
                                <input type="text" name="phone" class="form-control form-control-lg"
                                       value="{{ old('phone', $participant['phone'] ?? '') }}"
                                       placeholder="08xxxxxxxxxx" required>
                            </div>
                            <div class="form-group col-12 col-md-6">
                                <label class="font-weight-bold">Email</label>
                                <input type="email" name="email" class="form-control form-control-lg"
                                       value="{{ old('email', $participant['email'] ?? '') }}"
                                       placeholder="nama@email.com" required>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-3">
                            <button type="submit" class="btn btn-primary btn-block-xs px-5">
                                Lanjut ke Instruksi
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .fs-responsive { font-size: clamp(1.1rem, 4vw, 1.575rem); }

    /* Tombol full-width di HP */
    @media (max-width: 575.98px) {
        .btn-block-xs { width: 100%; }
        .form-control-lg { font-size: 0.95rem; }
    }
</style>
@endsection
