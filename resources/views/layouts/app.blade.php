<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Dashboard') - {{ config('app.name') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- SB Admin 2 & Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/startbootstrap-sb-admin-2@4.1.4/css/sb-admin-2.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://yafahi-production.up.railway.app/datatables/css/dataTables.bootstrap5.min.css">

    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css" rel="stylesheet">

    @stack('styles')
</head>
<body id="page-top">

    <div id="wrapper">
        @include('layouts.sidebar') <!-- Sidebar -->

        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                @include('layouts.navbar') <!-- Navbar -->

                <div class="container-fluid">
                    @yield('content') <!-- Main Content -->
                </div>
            </div>

            @include('layouts.footer') <!-- Footer -->
        </div>
    </div>

    <!-- jQuery (hanya jika diperlukan DataTables) -->
    <script src="{{ asset('datatables/js/jquery-3.7.1.min.js') }}"></script>

    <!-- Bootstrap JS (hanya sekali, untuk dropdown dan komponen lainnya) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- SB Admin 2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/startbootstrap-sb-admin-2@4.1.4/js/sb-admin-2.min.js"></script>

    <!-- DataTables JS -->
    <script src="{{ asset('datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('datatables/js/dataTables.bootstrap5.min.js') }}"></script>

    <!-- Sidebar toggle (untuk mobile) -->
    <script>
        document.getElementById('sidebarToggleTop')?.addEventListener('click', function () {
            const sidebar = document.getElementById('accordionSidebar');
            sidebar.classList.toggle('collapse');
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.js"></script>

    @stack('scripts')
</body>
</html>
