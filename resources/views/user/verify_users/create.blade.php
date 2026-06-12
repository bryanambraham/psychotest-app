<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Data user</title>
    <script src="{{ asset('js/app.js') }}"></script>

    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <style>
        body { background-color: #f8f9fa; }
        .navbar { box-shadow: 0 2px 4px rgba(0,0,0,.04); }
    </style>
</head>
<body>
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-primary text-white font-weight-bold">
                        <i class="fas fa-user-check mr-2"></i> Verifikasi Data Peserta
                    </div>

                    <div class="card-body p-4">
                        <div class="alert alert-info border-0 shadow-sm mb-4">
                            <strong>Halo!</strong> Sebelum melanjutkan, mohon verifikasi dan lengkapi data diri Anda. Data ini akan digunakan sebagai identitas pada hasil ujian Anda.
                        </div>

                        <form method="POST" action="{{ route('verify.store') }}">
                            @csrf

                            <div class="form-group row">
                                <label for="name" class="col-md-4 col-form-label text-md-right font-weight-bold">Nama Lengkap</label>
                                <div class="col-md-6">
                                    {{-- Value default mengambil dari data login user --}}
                                    <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" required autofocus>
                                    @error('name')
                                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="email" class="col-md-4 col-form-label text-md-right font-weight-bold">Alamat Email</label>
                                <div class="col-md-6">
                                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" required>
                                    @error('email')
                                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="phone" class="col-md-4 col-form-label text-md-right font-weight-bold">Nomor Telepon / WA</label>
                                <div class="col-md-6">
                                    <input id="phone" type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" placeholder="Contoh: 08123456789" required>
                                    @error('phone')
                                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="position" class="col-md-4 col-form-label text-md-right font-weight-bold">Posisi yang Dilamar</label>
                                <div class="col-md-6">
                                    <input id="position" type="text" class="form-control @error('position') is-invalid @enderror" name="position" placeholder="Contoh: Staff IT / Manager" required>
                                    @error('position')
                                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-success px-4 font-weight-bold shadow-sm">
                                        Simpan & Lanjutkan <i class="fas fa-arrow-right ml-1"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
