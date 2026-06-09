<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Website Nonaktif</title>
    
    <script src="{{ asset('js/app.js') }}" defer></script>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous" defer></script>

    <style>
        /* Custom gradient untuk background gelap yang elegan */
        body {
            background: linear-gradient(135deg, #343a40 0%, #1d2124 100%);
        }
        
        /* Animasi mengambang pada ikon gembok */
        .lock-icon {
            animation: float 3s ease-in-out infinite;
            filter: drop-shadow(0 10px 10px rgba(0,0,0,0.5));
        }

        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-15px); }
            100% { transform: translateY(0px); }
        }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center vh-100 text-white m-0">

    <div class="container text-center px-4">
        
        {{-- Ikon Gembok menggunakan FontAwesome --}}
        <div class="mb-5">
            <i class="fas fa-lock text-danger lock-icon" style="font-size: 7rem;"></i>
        </div>
        
        {{-- Teks Informasi --}}
        <h1 class="display-4 font-weight-bold mb-3">Website Sedang Ditutup</h1>
        
        <p class="lead text-light mb-5" style="max-width: 600px; margin: 0 auto; line-height: 1.8;">
            Sistem saat ini sedang dalam mode pemeliharaan <strong>(Maintenance)</strong> atau sengaja dikunci oleh Administrator. Silakan kembali lagi nanti.
        </p>

        {{-- Tombol Refresh Halaman --}}
        <button onclick="window.location.reload();" class="btn btn-danger btn-lg px-5 py-3 shadow" style="border-radius: 50px; font-weight: 600;">
            <i class="fas fa-sync-alt mr-2"></i> Muat Ulang Halaman
        </button>

    </div>

</body>
</html>