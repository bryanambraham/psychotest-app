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

    <style>
        body { background-color: #f8f9fa; }
        .navbar { box-shadow: 0 2px 4px rgba(0,0,0,.04); }
    </style>
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white">
            <div class="container">
                <a class="navbar-brand font-weight-bold" href="{{ url('/') }}">
                    {{ config('app.name', 'Psychotest') }}
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
                                    <i class="fas fa-home mr-1"></i> Home
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
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item" href="{{ route('manage-exams.results') }}">
                                            <i class="fas fa-chart-bar mr-2 text-muted"></i> Hasil Ujian
                                        </a>
                                    </div>
                                </li>
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

        <main class="py-4">
            @yield('content')
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @yield('scripts')
</body>
</html>
