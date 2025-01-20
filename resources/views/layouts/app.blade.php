<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Default Title') - {{ env('APP_NAME', '') }}</title> <!-- Dynamic title -->

    <!-- Google Fonts (Poppins) -->

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3.3 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <!-- Toastr CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />

    @vite('resources/js/app.js')
    @yield('css')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://unpkg.com/gijgo@1.9.14/js/gijgo.min.js" type="text/javascript"></script>
    <link href="https://unpkg.com/gijgo@1.9.14/css/gijgo.min.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">



    <style>
        @import url('https://fonts.googleapis.com/css2?family=Quicksand:wght@300..700&display=swap');

        /* Custom styles */
        html,
        body {
            height: 100%;
            margin: 0;
            display: flex;
            flex-direction: column;
            font-family: 'Poppins', sans-serif;
        }

        .navbar-brand {
            font-family: 'Neoda', sans-serif;
            font-size: 1.5rem;
            color: #d62f0d;
        }

        .navbar {
            background-color: #f1e2d5 !important;
        }

        .navbar-collapse {
            font-family: "Quicksand", serif;
            font-weight: bold;
            font-size: 1rem;
        }

        .content {
            flex: 1;
        }

        .header-title {
            font-weight: 600;
            font-size: 1.8rem;
            color: rgb(51, 48, 46);
            text-transform: uppercase;
            letter-spacing: 1px;
            font-family: 'ChicagoFLF', sans-serif;

        }

        footer {
            background-color: #343a40;
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
    </style>
</head>


<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">{{ env('APP_NAME', '') }}</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav w-100">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a>
                    </li>
                    <!-- Include the NavigationMenu Component here -->
                    <x-navigation-menu></x-navigation-menu>

                    <li class="ms-auto nav-item">
                        <a class="nav-link pt-2 pb-0" href="#">24564৳</a>
                    </li>

                    <li class="nav-item  dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            {{ Auth()->user()->name }}
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="#">Profile</a></li>
                            @if (isAdmin())
                                <li><a class="dropdown-item" href="{{ route('users.index') }}">Users List</a></li>
                            @endif
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button class='dropdown-item' type="submit">Logout</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mt-2 content">
        <div class="d-flex justify-content-between">
            <div>
                <h2 class="header-title">@yield('title', 'Default Page Title')</h2>
            </div>
            <div>
                @yield('header_content')
            </div>

        </div>

        <hr class="my-1">
        <div class="content">
            @yield('content') <!-- Dynamic content -->
        </div>
    </div>

    <!-- Footer -->
    <footer>
        &copy; {{ date('Y') }} {{ env('APP_NAME') }}. All Rights Reserved.
    </footer>

    <!-- Bootstrap 5.3.3 JS and Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
    @stack('custom-scripts')



</body>


</html>
