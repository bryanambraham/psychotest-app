@extends('layouts.app')

@section('content')
<div class="container-fluid px-3 px-md-4 py-4 py-md-5">
    <div class="row justify-content-center">
        <div class="col-12 col-sm-10 col-md-8 col-lg-7">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4 p-md-5 text-center">

                    {{-- Icon --}}
                    <div class="mb-3">
                        <div class="d-inline-flex align-items-center justify-content-center bg-success rounded-circle"
                             style="width: clamp(60px, 15vw, 80px); height: clamp(60px, 15vw, 80px);">
                            <i class="fas fa-check text-white" style="font-size: clamp(1.4rem, 5vw, 2rem);"></i>
                        </div>
                    </div>

                    <div class="h2 mb-2 text-success font-weight-bold fs-responsive-xl">Selesai</div>
                    <h3 class="font-weight-bold mb-3 fs-responsive">Ujian telah dikumpulkan</h3>
                    <p class="text-muted mb-0">
                        Terima kasih, <strong>{{ $session->user->name }}</strong>.<br class="d-sm-none">
                        Jawaban Anda untuk <strong>{{ $exam->name }}</strong> sudah tersimpan.
                    </p>
                    <p class="text-muted mb-0">
                        Silahkan kembali ke beranda untuk menyelesaikan soal selanjutnya.
                    </p>

                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .fs-responsive     { font-size: clamp(1.1rem, 4vw, 1.575rem); }
    .fs-responsive-xl  { font-size: clamp(1.4rem, 5vw, 2rem); }
</style>
@endsection
