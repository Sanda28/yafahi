<nav style="background-color: #f8f9fa; border-bottom: 1px solid #dee2e6; padding: 10px;">
    <div style="max-width: 1200px; margin: auto; display: flex; justify-content: space-between; align-items: center;">
        <div style="display: flex; align-items: center; gap: 15px;">
            <!-- Logo -->
            <a href="{{ route('dashboard') }}" style="font-weight: bold; font-size: 20px; text-decoration: none; color: #333;">
                {{ config('app.name', 'Laravel') }}
            </a>

            <!-- Navigation Links -->
            <a href="{{ route('dashboard') }}" style="text-decoration: none; color: #333;">Dashboard</a>
            <a href="{{ route('absensi.scan.kamera') }}" style="text-decoration: none; color: #333;">Scan QR via Kamera</a>
            <a href="{{ route('absensi.qr') }}" style="text-decoration: none; color: #333;">Generate QR</a>

            @if(in_array(Auth::user()->role, ['admin', 'superadmin']))
                <a href="{{ route('absensi.rekap') }}" style="text-decoration: none; color: #333;">Rekap Absensi</a>
                <a href="{{ route('absensi.rekap.pdf') }}" style="text-decoration: none; color: #333;">Download PDF</a>
            @endif
        </div>

        <!-- User Menu -->
        <div style="display: flex; align-items: center; gap: 10px;">
            <span style="color: #555;">{{ Auth::user()->name }}</span>
            <a href="{{ route('profile.edit') }}" style="text-decoration: none; color: #007bff;">Profile</a>

            <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" style="background: none; border: none; color: #dc3545; cursor: pointer;">Logout</button>
            </form>
        </div>
    </div>
</nav>
