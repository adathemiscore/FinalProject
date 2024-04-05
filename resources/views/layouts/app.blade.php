<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Project') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Project') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    {{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"> --}}
    <link rel="stylesheet" href="path/to/font-awesome/css/font-awesome.min.css">
    {{-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous"> --}}
    
    <!-- Scripts -->
    {{-- @vite(['resources/sass/app.scss', 'resources/js/app.js']) --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <!-- Scripts -->
    {{-- @vite(['resources/sass/app.scss', 'resources/js/app.js']) --}}
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                @if (Sentinel::check())
                    @if( Sentinel::getUser()->roles()->first()->slug == 'admin')
                        <a class="navbar-brand" href="{{ url('/home') }}">
                            {{ config('app.name', 'Project') }}
                        </a>

                        <a class="navbar-brand">
                            <a class="nav-link" href="{{ url('/register') }}">{{ __('Create Customers') }}</a>
                        </a>

                        <a class="navbar-brand">
                            <a class="nav-link" href="{{ url('/feedbackadmin') }}">{{ __('FeedBack') }}</a>
                        </a>

                        <a class="navbar-brand">
                            <a class="nav-link" href="{{ url('/productsall') }}">{{ __('Product') }}</a>
                        </a>
                    @elseif(Sentinel::getUser()->roles()->first()->slug == 'seller')
                        <a class="navbar-brand" href="{{ url('/seller') }}">
                            {{ config('app.name', 'Project') }}
                        </a>
                        
                        <a class="navbar-brand">
                            <a class="nav-link" href="{{ url('/feedbackseller') }}">{{ __('FeedBack') }}</a>
                        </a>
                    @else
                        <a class="navbar-brand" href="{{ url('/buyer') }}">
                            {{ config('app.name', 'Project') }}
                        </a>
                        
                        <a class="navbar-brand">
                            <a class="nav-link" href="{{ url('/feedback') }}">{{ __('FeedBack') }}</a>
                        </a>
                    @endif
                @else
                    <a class="navbar-brand" href="#">
                        {{ config('app.name', 'Project') }}
                    </a>
                @endif
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->
                        {{-- @guest --}}
                            @if (Sentinel::check())
                               <li class="nav-item dropdown">
                                    <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                       Hello {{ Sentinel::getUser()->first_name }}
                                    </a>

                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                        @if(Sentinel::getUser()->roles()->first()->slug == 'admin')
                                            <a class="dropdown-item" href="{{ route('passwordreset') }}">
                                                {{ __('Password Reset') }}
                                            </a>
                                        @elseif(Sentinel::getUser()->roles()->first()->slug == 'buyer')
                                            <a class="dropdown-item" href="{{ route('passwordresetbuyer') }}">
                                                {{ __('Password Reset') }}
                                            </a>
                                        @else
                                            <a class="dropdown-item" href="{{ route('passwordresetseller') }}">
                                                {{ __('Password Reset') }}
                                            </a>
                                        @endif

                                        @if((Sentinel::getUser()->roles()->first()->slug == 'buyer' || Sentinel::getUser()->roles()->first()->slug == 'seller'))
                                            <a class="dropdown-item" href="{{ route('logout') }}"
                                            onclick="event.preventDefault();
                                                document.getElementById('logout-form').submit();">
                                                {{ __('Logout') }}
                                            </a>
                                        @elseif(Sentinel::getUser()->roles()->first()->slug == 'admin')
                                            <a class="dropdown-item" href="{{ route('logoutadmin') }}"
                                            onclick="event.preventDefault();
                                                document.getElementById('logout-form-admin').submit();">
                                                {{ __('Logout') }}
                                            </a>
                                        @endif

                                        {{-- //seller and buyer logout --}}
                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                            @csrf
                                        </form>

                                        {{-- admin logout --}}
                                        <form id="logout-form-admin" action="{{ route('logoutadmin') }}" method="POST" class="d-none">
                                            @csrf
                                        </form>
                                    </div>
                                </li>
                                @if( Sentinel::getUser()->roles()->first()->slug == 'seller')
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ url('/createproduct') }}">{{ __('Create Product') }}</a>
                                    </li>
                                 @endif

                                

                                @if( Sentinel::getUser()->roles()->first()->slug == 'buyer')
                                    @php
                                        $purchaseCount = DB::table('cart_items')
                                            ->where('buyer_id', '=', Sentinel::getUser()->id)
                                            ->where('purchase_id', '=', null)
                                            ->sum('quantity');
                                    @endphp
                                   
                                    <a href="/cart" class="btn">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" style="color: green" fill="currentColor" class="bi bi-cart3" viewBox="0 0 16 16">
                                            <path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .49.598l-1 5a.5.5 0 0 1-.465.401l-9.397.472L4.415 11H13a.5.5 0 0 1 0 1H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5M3.102 4l.84 4.479 9.144-.459L13.89 4zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4m7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4m-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2m7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2"/>
                                        </svg> @if($purchaseCount > 0)<span class="badge text-bg-danger rounded-circle translate-middle position-relative">{{$purchaseCount}}</span>@endif
                                    </a>
                                @endif


                            @else
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ url('/') }}">{{ __('Login') }}</a>
                                </li>
                           
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ url('/signup') }}">{{ __('SignUp') }}</a>
                                </li>

                            @endif

                           
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>


        {{-- <footer class="pt-4 pb-4 border-top">
            <div class="row">
              <div class="col-12 col-md">
                <img class="mb-2" src="/docs/5.3/assets/brand/bootstrap-logo.svg" alt="" width="15" height="10">
                <small class="d-block mb-3 text-body-secondary">&copy; {{ now()->format('Y-m-d') }}</small>
              </div>
              <div class="col-6 col-md">
                <h5>Features</h5>
                <ul class="list-unstyled text-small">
                  <li class="mb-1"><a class="link-secondary text-decoration-none" href="#">Cool stuff</a></li>
                  <li class="mb-1"><a class="link-secondary text-decoration-none" href="#">Random feature</a></li>
                </ul>
              </div>
              <div class="col-6 col-md">
                <h5>Resources</h5>
                <ul class="list-unstyled text-small">
                  <li class="mb-1"><a class="link-secondary text-decoration-none" href="#">Resource</a></li>
                  <li class="mb-1"><a class="link-secondary text-decoration-none" href="#">Resource name</a></li>
                </ul>
              </div>
              <div class="col-4 col-md">
                <h5>About</h5>
                <ul class="list-unstyled text-small">
                  <li class="mb-1"><a class="link-secondary text-decoration-none" href="#">Team</a></li>
                  <li class="mb-1"><a class="link-secondary text-decoration-none" href="#">Contact Us</a></li>
                </ul>
              </div>
            </div>
          </footer> --}}
    </div>
</body>
</html>
