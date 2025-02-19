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

    <!-- DarkReader CDN -->
    <script src="https://cdn.jsdelivr.net/npm/darkreader@4.9.67/darkreader.min.js"></script>

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


        .header-title {
            font-weight: 600;
            font-size: 1.7rem;
            color: rgb(51, 48, 46);
            text-transform: uppercase;
            letter-spacing: 1px;
            font-family: 'ChicagoFLF', sans-serif;
        }

        .sms-balance {
            background-color: rgba(25, 135, 84, 0.1) !important;
            color: #198754 !important;
            font-weight: 500;
        }
    </style>
    <style>
        :root {
            --sidebar-width: 280px;
            --sidebar-collapsed-width: 70px;
            --topnav-height: 64px;
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
        }

        /* Base Layout */
        body {
            margin: 0;
            padding: 0;
            min-height: 100vh;
            background: #f8f9fa;
            font-family: 'Poppins', sans-serif;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            width: var(--sidebar-width);
            background: #fff;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
            z-index: 1040;
            transition: all 0.3s ease;
        }

        .sidebar-header {
            padding: 1.5rem;
            display: flex;
            align-items: center;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .sidebar-nav {
            list-style: none;
            padding: 1rem 0;
            margin: 0;
        }

        .sidebar-item {
            padding: 0 1rem;
            margin: 0.25rem 0;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            color: #495057;
            text-decoration: none;
            border-radius: 0.5rem;
            transition: all 0.2s;
        }

        .sidebar-link i {
            font-size: 1.1rem;
            width: 1.5rem;
            margin-right: 0.75rem;
        }

        .sidebar-link:hover,
        .sidebar-link.active {
            color: var(--primary-color);
            background: rgba(67, 97, 238, 0.05);
        }

        /* Sidebar Dropdown */
        .sidebar .dropdown-menu {
            position: static !important;
            transform: none !important;
            padding: 0.5rem;
            margin: 0.25rem 0;
            border: none;
            box-shadow: none;
            background: transparent;
        }

        .sidebar .dropdown-item {
            padding: 0.75rem 1rem;
            margin: 0.25rem 0;
            color: #495057;
            border-radius: 0.5rem;
            transition: all 0.2s;
        }

        .sidebar .dropdown-item:hover {
            color: var(--primary-color);
            background: rgba(67, 97, 238, 0.05);
        }

        /* Top Navigation */
        .top-nav {
            position: fixed;
            right: 0;
            top: 0;
            left: var(--sidebar-width);
            height: var(--topnav-height);
            background: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
            z-index: 1030;
            transition: all 0.3s ease;
            padding: 0 1.5rem;
        }

        /* Main Content and Footer Styles */
        .main {
            margin-left: var(--sidebar-width);
            padding: calc(var(--topnav-height) + 1.5rem) 1.5rem 0;
            min-height: calc(100vh - var(--topnav-height));
            display: flex;
            flex-direction: column;
        }

        .content-wrapper {
            flex: 1 0 auto;
        }

        .footer {
            flex-shrink: 0;
            background: #fff;
            padding: 1rem 1.5rem;
            margin-top: 2rem;
            border-top: 1px solid rgba(0, 0, 0, 0.05);
        }

        .footer-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: #6c757d;
            font-size: 0.875rem;
        }

        .footer-links {
            display: flex;
            gap: 1.5rem;
        }

        .footer-link {
            color: #6c757d;
            text-decoration: none;
            transition: color 0.2s;
        }

        .footer-link:hover {
            color: var(--primary-color);
        }

        @media (max-width: 991.98px) {
            .main {
                margin-left: 0;
            }
        }

        body.sidebar-collapsed .main {
            margin-left: var(--sidebar-collapsed-width);
        }

        /* Collapsed State */
        body.sidebar-collapsed .sidebar {
            width: var(--sidebar-collapsed-width);
        }

        body.sidebar-collapsed .top-nav {
            left: var(--sidebar-collapsed-width);
        }

        body.sidebar-collapsed .main {
            margin-left: var(--sidebar-collapsed-width);
        }

        body.sidebar-collapsed .sidebar-link span,
        body.sidebar-collapsed .sidebar-brand-text {
            display: none;
        }

        body.sidebar-collapsed .sidebar-link {
            justify-content: center;
            padding: 0.75rem;
        }

        body.sidebar-collapsed .sidebar-link i {
            margin: 0;
        }

        /* Responsive */
        @media (max-width: 991.98px) {
            .sidebar {
                left: calc(-1 * var(--sidebar-width));
            }

            .top-nav {
                left: 0;
            }

            .main {
                margin-left: 0;
            }

            body.sidebar-mobile-open .sidebar {
                left: 0;
            }

            body.sidebar-mobile-open::after {
                content: '';
                position: fixed;
                inset: 0;
                background: rgba(0, 0, 0, 0.5);
                z-index: 1035;
            }
        }

        /* Theme Toggle Button */
        .theme-toggle {
            padding: 0.5rem;
            border-radius: 0.5rem;
            color: #6c757d;
            background: transparent;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .theme-toggle:hover {
            background: rgba(67, 97, 238, 0.05);
            color: var(--primary-color);
        }

        .theme-toggle i {
            font-size: 1.25rem;
        }

        /* Animation for icon switch */
        .theme-toggle .bi-moon-fill,
        .theme-toggle.dark-mode .bi-sun-fill {
            display: none;
        }

        .theme-toggle.dark-mode .bi-moon-fill {
            display: inline-block;
        }

        .theme-toggle .bi-sun-fill {
            display: inline-block;
        }
    </style>
</head>

<body class="sidebar-enabled">
    <div class="wrapper">
        <!-- Sidebar -->
        <nav class="sidebar">
            <div class="sidebar-header">

                <a class="navbar-brand" href="{{ route('dashboard') }}">
                    <i class="bi bi-grid-fill me-2"></i>{{ env('APP_NAME', 'YourApp') }}
                </a>

            </div>

            <ul class="sidebar-nav">
                <li class="sidebar-item">
                    <a href="{{ route('dashboard') }}"
                        class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="bi bi-grid-fill"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <x-navigation-menu />
            </ul>
        </nav>

        <div class="main">
            <!-- Top Navigation -->

            <nav class="top-nav pt-2">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <button id="sidebarToggle" class="btn btn-link p-0 me-3">
                            <i class="bi bi-list fs-4"></i>
                        </button>
                    </div>

                    <div class="d-flex align-items-center gap-3">
                        <!-- Add Theme Toggle Button -->
                        <button class="theme-toggle" id="themeToggle" title="Toggle dark mode">
                            <i class="bi bi-sun-fill"></i>
                            <i class="bi bi-moon-fill"></i>
                        </button>

                        <a href="{{ route('sms.settings') }}" class="nav-link">
                            <i class="bi bi-envelope-fill"></i>
                            <span class="badge bg-success sms-balance">{{ number_format($smsBalance ?? 0, 2) }}
                                Tk</span>
                        </a>

                        <div class="dropdown">
                            <a class="nav-link" href="#" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle fs-5"></i>
                                <span class="ms-2 d-none d-sm-inline">{{ Auth()->user()->name }}</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                @if (canAccess('admin'))
                                    <li><a class="dropdown-item" href="{{ route('users.index') }}">
                                            <i class="bi bi-people me-2"></i>Users List
                                        </a></li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                @endif
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button class="dropdown-item text-danger"><i
                                                class="bi bi-box-arrow-right me-2"></i>Logout</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Content Area -->
            <div class="content-wrapper">
                <!-- Page Header -->
                <div
                    class="d-flex flex-column flex-md-row align-items-start align-items-lg-center gap-3 justify-content-between mb-4">
                    <div>
                        <h2 class="header-title">@yield('title', 'Default Page Title')</h2>
                    </div>
                    <div class="ms-0 ms-md-auto">
                        @yield('header_content')
                    </div>
                </div>

                <!-- Main Content -->
                @yield('content')
            </div>

            <!-- Footer -->
            <footer class="footer">
                <div class="footer-content">
                    <div class="footer-copyright">
                        &copy; {{ date('Y') }} {{ env('APP_NAME', 'YourApp') }}. All rights reserved.
                    </div>
                    <div class="footer-links d-none d-md-flex">
                        <a href="#" class="footer-link">About</a>
                        <a href="#" class="footer-link">Privacy Policy</a>
                        <span class="text-muted">v1.0.0</span>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize DarkReader in disabled state
            DarkReader.disable();

            // Theme Toggle Handler
            const themeToggle = document.getElementById('themeToggle');

            // Check saved theme preference
            const isDarkMode = localStorage.getItem('darkMode') === 'true';
            if (isDarkMode) {
                DarkReader.enable({
                    brightness: 100,
                    contrast: 90,
                    sepia: 10
                });
                themeToggle.classList.add('dark-mode');
            }

            // Theme Toggle Click Handler
            themeToggle.addEventListener('click', () => {
                if (themeToggle.classList.contains('dark-mode')) {
                    DarkReader.disable();
                    themeToggle.classList.remove('dark-mode');
                    localStorage.setItem('darkMode', 'false');
                } else {
                    DarkReader.enable({
                        brightness: 100,
                        contrast: 90,
                        sepia: 10
                    });
                    themeToggle.classList.add('dark-mode');
                    localStorage.setItem('darkMode', 'true');
                }
            });

            // Sidebar Toggle
            $('#sidebarToggle').click(function(e) {
                e.preventDefault();
                $('body').toggleClass('sidebar-collapsed');
                localStorage.setItem('sidebar-collapsed', $('body').hasClass('sidebar-collapsed'));
            });

            // Restore sidebar state
            if (localStorage.getItem('sidebar-collapsed') === 'true') {
                $('body').addClass('sidebar-collapsed');
            }

            // Mobile sidebar handling
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.sidebar').length && !$(e.target).closest('#sidebarToggle')
                    .length) {
                    $('body').removeClass('sidebar-mobile-open');
                }
            });

            $('#sidebarToggle').on('click', function(e) {
                e.preventDefault();
                if (window.innerWidth < 992) {
                    $('body').toggleClass('sidebar-mobile-open');
                }
            });
        });

        @if (session('message'))
            toastr.success("{{ session('message') }}");
        @endif
    </script>

    @yield('custom-scripts')
    @yield('scripts')
</body>

</html>
