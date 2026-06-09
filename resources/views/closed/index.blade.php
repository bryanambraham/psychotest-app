<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Website Nonaktif</title>
    <script src="{{ asset('js/app.js') }}"></script>

    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous" defer></script>
</head>
<body class="bg-gradient-to-br from-gray-900 to-gray-700 font-sans antialiased text-white min-h-screen flex items-center justify-center p-4">

    <div class="text-center space-y-6">
        {{-- Ikon Gembok sebagai indikator visual --}}
        <svg class="w-24 h-24 mx-auto text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"></path>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v2.28a2 2 0 00.511 1.638l5.489 5.489a2 2 0 001.638.511h2.28a2 2 0 002-2v-5a2 2 0 00-2-2z"></path>
        </svg>

        <h1 class="text-5xl font-extrabold text-red-500 tracking-wide">AKSES DITANGGUHKAN</h1>

        <p class="text-xl font-medium text-gray-300">
            Situs web ini sedang tidak aktif.
        </p>

        <p class="text-lg font-light text-gray-400">
            Silakan hubungi pengembang untuk informasi lebih lanjut atau cek kembali nanti.
        </p>

        {{-- Menambahkan tautan atau informasi kontak --}}
        {{-- <a href="#" class="inline-block mt-4 px-6 py-3 border-2 border-red-500 text-red-500 font-semibold rounded-full hover:bg-red-500 hover:text-white transition duration-300 ease-in-out">
            Hubungi Pengembang
        </a> --}}
    </div>
</body>
</html>