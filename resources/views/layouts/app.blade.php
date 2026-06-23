<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Psychotest App') }}</title>

    <script src="{{ asset('js/app.js') }}"></script>

    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://bossanova.uk/jspreadsheet/v4/jexcel.css" type="text/css" />
    <link rel="stylesheet" href="https://jsuites.net/v4/jsuites.css" type="text/css" />

    <style>
        body { 
            background-image: url('{{ asset('background.jpg') }}'); 
            background-size: cover; /* Agar gambar memenuhi layar */
            background-position: center center; /* Posisi gambar di tengah */
            background-repeat: no-repeat; /* Agar gambar tidak berulang */
            background-attachment: fixed; /* Agar gambar tetap di tempat saat halaman di-scroll */
            background-color: #f8f9fa; 
        }
        .navbar { box-shadow: 0 2px 4px rgba(0,0,0,.04); }
    </style>
</head>
<body>
    <div id="app" class="font-weight-bold">
        <nav class="navbar navbar-expand-md navbar-light">
            <div class="container">
                <a class="navbar-brand font-weight-bold" href="{{ url('/') }}">
                    <img class="logo-navbar" src="{{ asset('logo_gl_trans.png') }}" alt="{{ env('APP_NAME') }}">
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">
                        @auth
                            <li class="nav-item {{ request()->routeIs('home') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('home') }}">
                                    <i class="fas fa-home mr-1"></i> Beranda
                                </a>
                            </li>

                            {{-- Menu Khusus Admin --}}
                            @if(auth()->user()->role == 'admin')
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" id="adminDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-cogs mr-1"></i> Administrasi
                                    </a>
                                    <div class="dropdown-menu shadow-sm" aria-labelledby="adminDropdown">
                                        <a class="dropdown-item" href="{{ route('manage-exams.index') }}">
                                            <i class="fas fa-edit mr-2 text-muted"></i> Kelola Ujian
                                        </a>
                                        <a class="dropdown-item" href="{{ route('users.index') }}">
                                            <i class="fas fa-users mr-2 text-muted"></i> Kelola User
                                        </a>
                                        <a class="dropdown-item" href="{{ route('verify_users.index') }}">
                                            <i class="fas fa-users mr-2 text-muted"></i> Kelola Verifikasi User
                                        </a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item" href="{{ route('manage-exams.results') }}">
                                            <i class="fas fa-chart-bar mr-2 text-muted"></i> Hasil Ujian
                                        </a>
                                    </div>
                                </li>

                                <form action="{{ route('admin.toggle-site-closed') }}" method="POST" class="d-inline ml-4">
                                    @csrf
                                    @if(\Illuminate\Support\Facades\Cache::get('site_closed_mode', false))
                                        {{-- Jika sedang tutup, tombol berwarna Hijau untuk membuka --}}
                                        <button type="submit" class="btn btn-success font-weight-bold shadow-sm">
                                            <i class="fas fa-unlock"></i> Website Sedang CLOSED (Klik untuk Buka)
                                        </button>
                                    @else
                                        {{-- Jika sedang normal, tombol berwarna Merah untuk menutup --}}
                                        <button type="submit" class="btn btn-danger font-weight-bold shadow-sm" onclick="return confirm('Yakin ingin menutup website dari publik?')">
                                            <i class="fas fa-lock"></i> Kunci Website (Set to Closed)
                                        </button>
                                    @endif
                                </form>
                            @endif

                            @if(auth()->user()->name ==  strtolower(config('app.admin_name'))) 
                                <a class="mx-3 btn btn-success font-weight-bold shadow-sm" href="{{ route('activity-log.index') }}">Activity Log</a>
                            @endif
                        @endauth
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        @guest
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">
                                    <i class="fas fa-sign-in-alt mr-1"></i> {{ __('Login') }}
                                </a>
                            </li>
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle font-weight-bold" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span class="badge badge-pill badge-primary mr-2">{{ strtoupper(auth()->user()->role) }}</span>
                                    Halo, {{ Str::before(Auth::user()->name, ' ') }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-right shadow-sm border-0" aria-labelledby="navbarDropdown">
                                    <div class="dropdown-header">Pengaturan Akun</div>
                                    <a class="dropdown-item text-danger" href="{{ route('logout') }}"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="fas fa-sign-out-alt mr-2"></i> {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4 font-weight-bold">
            @yield('content')
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- table untuk kasus (akuntansi) -->
    <script src="https://bossanova.uk/jspreadsheet/v4/jexcel.js"></script>
    <script src="https://jsuites.net/v4/jsuites.js"></script>
    
    @yield('scripts')
</body>
</html>
