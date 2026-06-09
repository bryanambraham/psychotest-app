@extends('layouts.app')

@section('content')
<div class="container-fluid px-3 px-md-4 py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-sm-11 col-lg-8">

            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="card shadow-sm border-0">
                <div class="card-body p-3 p-md-5">

                    {{-- Header --}}
                    <div class="mb-4">
                        <h3 class="font-weight-bold mb-2 fs-responsive">Persiapan Pengerjaan</h3>
                        <p class="text-muted mb-0 small">Data peserta sudah tersimpan. Saat Anda menekan tombol mulai, timer akan langsung berjalan dan halaman soal akan dibuka.</p>
                    </div>

                    @if ($exam->type == 'pilgan')
                        <div class="mb-4">
                            <span class="badge badge-danger mb-2">INSTRUKSI</span>
                            <p class="font-weight-bold text-red-600 mb-0 medium">Pilihlah opsi jawaban yang menurut anda paling benar di antara kumpulan opsi jawaban yang ada.</p>
                        </div>
                    @elseif($exam->type == 'tes_kraeplin')
                        <div class="mb-4">
                            <span class="badge badge-danger mb-2">INSTRUKSI</span>
                            <p class="font-weight-bold text-red-600 mb-0 medium">Pada soal yang akan kamu kerjakan ini, ada sejumlah soal menghitung sederhana yang dapat dilakukan setiap orang  
dengan latar belakang pendidikan sekolah dasar. Anda diminta untuk mengerjakan dengan cepat
dan tepat. Untuk itu Anda perlu memperhatikan bahwa Tanda Tambah (  +  ) mempunyai arti 
pembagian (  :  ), Tanda Bagi (  :  ) mempunyai arti pengurangan (  -  ), Tanda Kurang (  -  ) 
mempunyai arti perkalian (  x  ), dan Tanda Kali (  x  ) mempunyai arti penambahan (  +  ).
Hasil pekerjaan Anda sangat tergantung pada kecepatan dan ketelitian
                            </p>
                        </div>
                    @elseif($exam->type == 'angka')
                        <div class="mb-4">
                            <span class="badge badge-danger mb-2">INSTRUKSI</span>
                            <p class="font-weight-bold text-red-600 mb-0 medium">Siapkan kalkulator anda. Anda akan diminta untuk menjumlahkan angka-angka yang tertera secara mendatar dan menurun.</p>
                        </div>  
                    @elseif($exam->type == 'uraian')
                        <div class="mb-4">
                            <span class="badge badge-danger mb-2">INSTRUKSI</span>
                            <p class="font-weight-bold text-red-600 mb-0 medium">Pilihlah opsi jawaban yang menurut anda paling benar di antara kumpulan opsi jawaban yang ada.</p>
                        </div>  
                    @elseif($exam->type == 'disc')
                        <div class="mb-4">
                            <span class="badge badge-danger mb-2">INSTRUKSI</span>
                            <ul class="font-weight-bold text-red-600 mb-0 medium">
                                <li>Pilih	1	(satu)	huruf	yang	Paling	Mirip	kepribadian	Anda	dan	letakkan	jawabannya	di	kotak	"Mirip".</li>
                                <li>Pilih	1	(satu)	huruf	yang	Paling	Tidak	Mirip	kepribadian	Anda	dan	letakkan	jawabannya	di	kotak	"Tidak	Mirip"</li>
                                <li>Jadi,	di	setiap	kotak	hanya	akan	ada	1	Paling	Mirip	dan	1	Paling	Tidak	Mirip</li>
                            </ul>
                        </div>  
                    @elseif($exam->type == 'soal_kasus')
                        <div class="mb-4">
                            <span class="badge badge-danger mb-2">INSTRUKSI</span>
                            <p class="font-weight-bold text-red-600 mb-0 medium">Siapkan kalkulator anda. Telah kami sediakan soal kasus yang akan anda kerjakan, simak baik-baik soal tersebut dan jawablah pertanyaan atau perintah yang tertera.</p>
                        </div>                 
                    @endif


                    {{-- Participant Info Cards --}}
                    <div class="row mb-4">
                        <div class="col-12 col-sm-4 mb-2 mb-sm-0">
                            <div class="p-3 border rounded bg-light h-100">
                                <div class="small text-muted">Nama</div>
                                <div class="font-weight-bold text-break">{{ $participant['name'] }}</div>
                            </div>
                        </div>
                        <div class="col-6 col-sm-4 mb-2 mb-sm-0">
                            <div class="p-3 border rounded bg-light h-100">
                                <div class="small text-muted">Telepon</div>
                                <div class="font-weight-bold text-break">{{ $participant['phone'] }}</div>
                            </div>
                        </div>
                        <div class="col-6 col-sm-4">
                            <div class="p-3 border rounded bg-light h-100">
                                <div class="small text-muted">Email</div>
                                <div class="font-weight-bold text-break" style="font-size: 0.85rem;">{{ $participant['email'] }}</div>
                            </div>
                        </div>
                    </div>

                    {{-- Alert Tips --}}
                    <div class="alert alert-info border-0">
                        <ul class="mb-0 pl-3">
                            <li>Pastikan koneksi internet stabil.</li>
                            <li>Siapkan perangkat yang akan digunakan sampai ujian selesai.</li>
                            <li>Timer dimulai tepat setelah halaman soal terbuka.</li>
                        </ul>
                    </div>

                    {{-- Submit --}}
                    <form method="POST" action="{{ route('exam.begin', $exam) }}"
                          class="d-flex justify-content-end mt-4">
                        @csrf
                        <button type="submit" class="btn btn-success btn-block-xs px-5">
                            Mulai Ujian
                        </button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .fs-responsive { font-size: clamp(1.1rem, 4vw, 1.575rem); }

    @media (max-width: 575.98px) {
        .btn-block-xs { width: 100%; }
    }
</style>
@endsection