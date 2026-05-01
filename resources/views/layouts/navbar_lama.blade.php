                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav mr-auto"></ul>
                        @auth
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('home') }}">Home</a>
                            </li>
                        @endauth
                        @auth
                            {{-- Hanya muncul jika role user adalah admin --}}
                            @if(auth()->user()->role == 'admin')
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('manage-exams.index') }}">⚙️ Kelola Ujian</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('users.index') }}">👥 Kelola User</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('manage-exams.results') }}">📊 Kelola Hasil Ujian</a>
                                </li>
                            @endif
                        @endauth
                        {{-- @auth
                            <li class="nav-item">
                                <a class="nav-link font-weight-bold text-primary" href="{{ route('exam.show', ['exam_id' => 1]) }}">
                                    📝 Mulai Ujian (Demo)
                                </a>
                            </li>
                        @endauth --}}
                    <ul class="navbar-nav ml-auto">
                        @guest
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                            </li>
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                                    {{ Str::before(Auth::user()->name, ' ') }} <span class="caret"></span>
                                </a>

                                <div class="dropdown-menu dropdown-menu-right">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
