<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Stok Raw Material</title>
    <style>
        body {
            font-family: sans-serif;
            margin: 20px;
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

        .alert {
            padding: 10px;
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            margin-bottom: 20px;
        }

        .btn {
            padding: 3px 7px;
            text-decoration: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn-primary {
            background-color: #007bff;
            color: white;
            border: none;
        }

        .btn-warning {
            background-color: #ffc107;
            color: black;
            border: none;
        }

        .btn-danger {
            background-color: #dc3545;
            color: white;
            border: none;
        }

        .btn-group form {
            display: inline-block;
            margin-left: 5px;
        }

        .critical-stock {
            background-color: #f8d7da;
            color: #721c24;
            font-weight: bold;
        }

        /* Gaya untuk stok kritis */
    </style>
</head>

<body>
    <h1>Manajemen Stok Raw Material</h1>

    @if (session('success'))
        <div class="alert">
            {{ session('success') }}
        </div>
    @endif

    <div style="margin-bottom: 20px;">
        <a href="{{ route('stocks.create') }}" class="btn btn-primary">Tambah Stok Baru</a>
        {{-- Tombol Refresh All --}}
        <form action="{{ route('stocks.refresh.all') }}" method="POST" style="display: inline-block; margin-left: 10px;">
            @csrf
            <button type="submit" class="btn btn-primary" style="background-color: #6c757d;">Refresh Semua Status
                Stok</button>
        </form>
    </div>

    @if ($stocks->isEmpty())
        <p>Belum ada data stok raw material.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Raw Material</th>
                    <th>Stok Ready</th>
                    <th>Stok Dalam Proses</th>
                    <th>Status Proses</th>
                    <th>Estimasi Habis</th> {{-- Tambahkan baris ini --}}
                    <th>Kedaluwarsa</th> {{-- Tambahkan baris ini --}}
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($stocks as $stock)
                    {{-- Tambahkan kelas 'critical-stock' jika is_critical true --}}
                    <tr class="{{ $stock->is_critical ? 'critical-stock' : '' }}">
                        <td>{{ $stock->id }}</td>
                        <td>{{ $stock->rawMaterial->name }}</td>
                        <td>{{ $stock->ready_stock }}</td>
                        <td>{{ $stock->in_process_stock }}</td>
                        <td>{{ $stock->process_status ?? '-' }}</td> {{-- Tampilkan '-' jika null --}}
                        <td>
                            @if ($stock->estimated_depletion_date)
                                {{ $stock->estimated_depletion_date->format('d M Y') }}
                            @else
                                Aman / Belum ada kebutuhan
                            @endif
                        </td> {{-- Tambahkan baris ini --}}
                        <td>
                            @if ($stock->expired_date)
                                {{ $stock->expired_date->format('d M Y') }}
                            @else
                                -
                            @endif
                        </td> {{-- Tambahkan baris ini --}}
                        <td class="btn-group">
                            <a href="{{ route('stocks.edit', $stock->id) }}" class="btn btn-warning">Edit</a>
                            <form action="{{ route('stocks.destroy', $stock->id) }}" method="POST"
                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</body>

</html>
