<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Default Title') - {{ env('APP_NAME', 'YourApp') }}</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

    <!-- Google Fonts (Poppins) -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3.3 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <!-- Toastr CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />

    @if (env('AUTO_RELOAD', false))
        @vite('resources/js/app.js')
    @endif

    @yield('css')

    <!-- External Libraries -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://unpkg.com/gijgo@1.9.14/js/gijgo.min.js" type="text/javascript"></script>
    <link href="https://unpkg.com/gijgo@1.9.14/css/gijgo.min.css" rel="stylesheet" type="text/css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <link href="{{ asset('css/style.css') }}" rel="stylesheet">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Quicksand:wght@300..700&display=swap');

        html,
        body {
            height: 100%;
            margin: 0;
            display: flex;
            flex-direction: column;
            font-family: 'Poppins', sans-serif;
        }

        .navbar {
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.05);
            padding: 1rem 0;
            margin-bottom: 20px;
        }

        .navbar-brand {
            font-family: 'Poppins', sans-serif;
            font-size: 1.6rem;
            font-weight: 600;
            color: #2c3e50;
            transition: color 0.3s ease;
        }

        .navbar-brand:hover {
            color: #3498db;
        }

        .nav-link {
            color: #34495e;
            font-weight: 500;
            padding: 0.5rem 1rem;
            margin: 0 0.2rem;
            border-radius: 6px;
            transition: all 0.3s ease;
        }

        .nav-link:hover {
            color: #3498db;
            background: rgba(52, 152, 219, 0.1);
        }

        .nav-link.active {
            color: #3498db;
            background: rgba(52, 152, 219, 0.15);
        }

        .dropdown-menu {
            border: none;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border-radius: 12px;
            padding: 0.5rem;
            min-width: 220px;
            animation: dropdownFade 0.3s ease;
        }

        .dropdown-item {
            padding: 0.7rem 1.2rem;
            border-radius: 6px;
            margin: 2px 0;
            transition: all 0.2s ease;
        }

        .dropdown-item:hover {
            background: rgba(52, 152, 219, 0.1);
            color: #3498db;
            transform: translateX(5px);
        }

        @keyframes dropdownFade {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .navbar-toggler {
            border: none;
            padding: 0.5rem;
        }

        .navbar-toggler:focus {
            box-shadow: none;
            outline: none;
        }

        .navbar-toggler-icon {
            width: 24px;
            height: 24px;
        }

        .content {
            flex: 1;
        }

        .header-title {
            font-weight: 600;
            font-size: 1.7rem;
            color: rgb(51, 48, 46);
            text-transform: uppercase;
            letter-spacing: 1px;
            font-family: 'ChicagoFLF', sans-serif;
        }

        footer {
            background-color: rgb(51, 48, 46);
            color: white;
            text-align: center;
            padding: 20px;
        }

        .navbar {
            margin-bottom: 10px;
        }

        .dropdown-menu {
            min-width: 200px;
        }

        .fl-wrapper {
            margin-top: 45px;
        }

        .nav-item .badge {
            font-size: 0.85em;
            padding: 0.35em 0.65em;
            transition: all 0.3s ease;
        }

        .nav-link:hover .badge {
            transform: scale(1.05);
        }

        .sms-balance {
            background-color: rgba(25, 135, 84, 0.1) !important;
            color: #198754 !important;
            font-weight: 500;
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="bi bi-grid-fill me-2"></i>{{ env('APP_NAME', 'YourApp') }}
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <i class="bi bi-list"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav w-100">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                            href="{{ route('dashboard') }}">
                            <i class="bi bi-house-door me-1"></i>Dashboard
                        </a>
                    </li>

                    <x-navigation-menu></x-navigation-menu>

                    <!-- Add SMS Balance Display -->
                    <li class="nav-item ms-auto">
                        <a class="nav-link" href="{{ route('sms.settings') }}">
                            <i class="bi bi-envelope-fill me-1"></i>
                            Balance: <span class="badge bg-success sms-balance">{{ number_format($smsBalance ?? 0, 2) }}
                                Tk</span>
                        </a>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarDropdown"
                            role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle me-2"></i>
                            {{ Auth()->user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            @if (canAccess('admin'))
                                <li>
                                    <a class="dropdown-item" href="{{ route('users.index') }}">
                                        <i class="bi bi-people me-2"></i>Users List
                                    </a>
                                </li>
                            @endif
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button class='dropdown-item' type="submit">
                                        <i class="bi bi-box-arrow-right me-2"></i>Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Add margin-top to content to account for fixed navbar -->
    <div class="container mt-5 pt-5 content">
        <div
            class="d-flex flex-column flex-md-row align-items-start align-items-lg-center gap-3 justify-content-between">
            <div>
                <h2 class="header-title">@yield('title', 'Default Page Title')</h2>
            </div>
            <div class="ms-0 ms-md-auto mt-2 mt-md-0">
                @yield('header_content')
            </div>
        </div>

        <hr class="my-1">
        <div class="content">
            @yield('content')
        </div>
    </div>

    <!-- Footer -->
    <footer>
        &copy; {{ date('Y') }} {{ env('APP_NAME', 'YourApp') }}. All Rights Reserved.
    </footer>

    <!-- Bootstrap JS and Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>

    <!-- Toastr -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <!-- Display Flash Messages -->
    @if (session('message'))
        <script>
            toastr.success("{{ session('message') }}");
        </script>
    @endif

    @yield('custom-scripts')
    @yield('scripts')
</body>

</html>
