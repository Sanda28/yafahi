<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('dashboard') }}">
        <div class="sidebar-brand-icon">
            <img src="{{ asset('assets/images/logo.png') }}" alt="Yafahi Logo" style="width: 40px; height: 40px; object-fit: contain;">
        </div>
        <div class="sidebar-brand-text mx-3">Yafahi</div>
    </a>


    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Dashboard -->
    <li class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('dashboard') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <li class="nav-item {{ request()->routeIs('absensi.index') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('absensi.index') }}">
            <i class="fas fa-fw fa-calendar-check"></i>
            <span>Riwayat Absensi</span>
        </a>
    </li>

    <!-- User-specific or Admin/Superadmin -->
    @if(Auth::user()->role === 'user')
        <li class="nav-item {{ request()->routeIs('absensi.scan.kamera') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('absensi.scan.kamera') }}">
                <i class="fas fa-fw fa-qrcode"></i>
                <span>Scan QR</span>
            </a>
        </li>
        <li class="nav-item {{ request()->routeIs('izin.create') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('izin.create') }}">
                <i class="fas fa-fw fa-paper-plane"></i>
                <span>Ajukan Izin</span>
            </a>
        </li>
        <li class="nav-item {{ request()->routeIs('izin.my') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('izin.my') }}">
                <i class="fas fa-fw fa-clock"></i>
                <span>Riwayat Izin</span>
            </a>
        </li>
    @endif

    @if(in_array(Auth::user()->role, ['admin', 'superadmin']))
        <li class="nav-item {{ request()->routeIs('absensi.generate.qr') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('absensi.generate.qr') }}">
                <i class="fas fa-fw fa-qrcode"></i>
                <span>Generate QR</span>
            </a>
        </li>
        <li class="nav-item {{ request()->routeIs('izin.index') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('izin.index') }}">
                <i class="fas fa-fw fa-check-circle"></i>
                <span>Persetujuan Izin</span>
            </a>
        </li>

        <li class="nav-item {{ request()->routeIs('admin.logs.index') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.logs.index') }}">
                <i class="fas fa-fw fa-history"></i>
                <span>Log Aktivitas</span>
            </a>
        </li>
        <li class="nav-item {{ request()->routeIs('admin.users.index') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.users.index') }}">
                <i class="fas fa-fw fa-users-cog"></i>
                <span>Manajemen User</span>
            </a>
        </li>
        @if(Auth::user()->role === 'superadmin')
            <li class="nav-item {{ request()->routeIs('superadmin.admins.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('superadmin.admins.index') }}">
                    <i class="fas fa-fw fa-user-shield"></i>
                    <span>Kelola Admin</span>
                </a>
            </li>
        @endif

        <li class="nav-item {{ request()->routeIs('admin.libur.index') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.libur.index') }}">
                <i class="fas fa-fw fa-calendar-times"></i>
                <span>Manajemen Hari Libur</span>
            </a>
        </li>
        <li class="nav-item {{ request()->routeIs('admin.jadwal.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.jadwal.index') }}">
                <i class="fas fa-fw fa-calendar-alt"></i>
                <span>Jadwal Guru</span>
            </a>
        </li>
        <li class="nav-item {{ request()->routeIs('laporan.bulanan') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('laporan.bulanan') }}">
                <i class="fas fa-fw fa-calendar-alt"></i>
                <span>Laporan Bulanan</span>
            </a>
        </li>
    @endif

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Logout -->
    <li class="nav-item">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="btn btn-primary w-100 text-start">
                <i class="fas fa-sign-out-alt me-2"></i> Logout
            </button>
        </form>
    </li>

</ul>
