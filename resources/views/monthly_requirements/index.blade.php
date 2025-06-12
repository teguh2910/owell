<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kebutuhan Raw Material Bulanan</title>
    <style>
        body { font-family: sans-serif; margin: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .alert { padding: 10px; background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; margin-bottom: 20px; }
        .alert-danger { color: #721c24; background-color: #f8d7da; border-color: #f5c6cb; padding: 10px; margin-bottom: 10px; border: 1px solid; border-radius: 4px; }
        .btn { padding: 8px 12px; text-decoration: none; border-radius: 4px; cursor: pointer; }
        .btn-primary { background-color: #007bff; color: white; border: none; }
        .btn-warning { background-color: #ffc107; color: black; border: none; }
        .btn-danger { background-color: #dc3545; color: white; border: none; }
        .btn-group form { display: inline-block; margin-left: 5px; }
    </style>
</head>
<body>
    <h1>Kebutuhan Raw Material Bulanan</h1>

    @if (session('success'))
        <div class="alert">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <a href="{{ route('monthly_requirements.create') }}" class="btn btn-primary">Input Kebutuhan Baru</a>

    @if ($monthlyRequirements->isEmpty())
        <p>Belum ada data kebutuhan bulanan.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Raw Material</th>
                    <th>Tahun</th>
                    <th>Bulan</th>
                    <th>Total Bulanan</th>
                    <th>Minggu 1</th>
                    <th>Minggu 2</th>
                    <th>Minggu 3</th>
                    <th>Minggu 4</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($monthlyRequirements as $req)
                    <tr>
                        <td>{{ $req->id }}</td>
                        <td>{{ $req->rawMaterial->name }}</td> {{-- Mengakses nama dari relasi --}}
                        <td>{{ $req->year }}</td>
                        <td>{{ date("F", mktime(0, 0, 0, $req->month, 1)) }}</td> {{-- Konversi angka bulan ke nama bulan --}}
                        <td>{{ $req->total_monthly_usage }}</td>
                        <td>{{ $req->weekly_usage_1 }}</td>
                        <td>{{ $req->weekly_usage_2 }}</td>
                        <td>{{ $req->weekly_usage_3 }}</td>
                        <td>{{ $req->weekly_usage_4 }}</td>
                        <td class="btn-group">
                            <a href="{{ route('monthly_requirements.edit', $req->id) }}" class="btn btn-warning">Edit</a>
                            <form action="{{ route('monthly_requirements.destroy', $req->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
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