<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Management Stock Owell')</title>
    <style>
        body {
            font-family: sans-serif;
            margin: 0;
            background-color: #f4f4f4;
        }

        header {
            background-color: #333;
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        header h1 {
            margin: 0;
            font-size: 24px;
        }

        nav ul {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
        }

        nav ul li {
            margin-left: 20px;
        }

        nav ul li a {
            color: white;
            text-decoration: none;
            font-weight: bold;
        }

        nav ul li a:hover {
            text-decoration: underline;
        }

        .container {
            margin: 20px;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .alert {
            padding: 10px;
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            margin-bottom: 20px;
            border-radius: 4px;
        }

        .alert-danger,
        .alert-error {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid;
            border-radius: 4px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .btn {
            padding: 4px 4px;
            text-decoration: none;
            border-radius: 4px;
            cursor: pointer;
            border: none;
            display: inline-block;
        }

        .btn-primary {
            background-color: #007bff;
            color: white;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .btn-warning {
            background-color: #ffc107;
            color: black;
        }

        .btn-warning:hover {
            background-color: #e0a800;
        }

        .btn-danger {
            background-color: #dc3545;
            color: white;
        }

        .btn-danger:hover {
            background-color: #c82333;
        }

        /* Perbaiki CSS untuk critical-stock agar tidak tertimpa Datatables */
        /* Pilihan 1: Jadikan lebih spesifik (direkomendasikan) */
        table.dataTable tbody tr.critical-stock {
            background-color: #f8d7da !important;
            /* Tambahkan !important untuk memastikan override */
            color: #721c24 !important;
            font-weight: bold !important;
        }

        .btn-group form {
            display: inline-block;
            margin-left: 5px;
        }
    </style>

    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.min.css">
</head>

<body>
    <header>
        <h1>Management Stock Owell</h1>
        <nav>
            <ul>
                {{-- Navigasi Utama --}}
                @if (Auth::user()->role == 'admin' || Auth::user()->role == 'ppic')
                    <li><a href="{{ route('raw_materials.index') }}">Master Raw Material</a></li>
                @endif

                @if (Auth::user()->role == 'admin' || Auth::user()->role == 'ppic')
                    <li><a href="{{ route('monthly_requirements.index') }}">Kebutuhan Bulanan</a></li>
                @endif

                @if (Auth::user()->role == 'admin' || Auth::user()->role == 'ppic' || Auth::user()->role == 'supplier')
                    <li><a href="{{ route('stocks.index') }}">Manajemen Stok</a></li>
                @endif

            </ul>

        </nav>
        <div class="user-info">
            @auth
                <span>Selamat datang, {{ Auth::user()->name }} ({{ Auth::user()->role }})</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit">Logout</button> {{-- Tombol Logout saat ini --}}
                </form>
            @else
                <a href="{{ route('login') }}" style="color: white; text-decoration: none;">Login</a>
            @endauth
        </div>
    </header>

    <div class="container">
        @if (session('success'))
            <div class="alert">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert-error">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert-danger">
                <strong>Terjadi kesalahan:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                    @if (session('errors') && session('errors')->has('excel_errors'))
                        @foreach (session('errors')->get('excel_errors') as $excelError)
                            <li>{{ $excelError }}</li>
                        @endforeach
                    @endif
                </ul>
            </div>
        @endif

        @yield('content')
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
    @stack('scripts') {{-- Ini penting untuk script halaman spesifik --}}
</body>

</html>
