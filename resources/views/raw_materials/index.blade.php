<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Master Data Raw Material</title>
    <style>
        body { font-family: sans-serif; margin: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .alert { padding: 10px; background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; margin-bottom: 20px; }
        .btn { padding: 8px 12px; text-decoration: none; border-radius: 4px; cursor: pointer; }
        .btn-primary { background-color: #007bff; color: white; border: none; }
        .btn-warning { background-color: #ffc107; color: black; border: none; }
        .btn-danger { background-color: #dc3545; color: white; border: none; }
        .btn-group form { display: inline-block; margin-left: 5px; }
    </style>
</head>
<body>
    <h1>Master Data Raw Material</h1>

    @if (session('success'))
        <div class="alert">
            {{ session('success') }}
        </div>
    @endif

    <a href="{{ route('raw_materials.create') }}" class="btn btn-primary">Tambah Raw Material Baru</a>

    @if ($rawMaterials->isEmpty())
        <p>Belum ada data Raw Material.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama Raw Material</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rawMaterials as $rawMaterial)
                    <tr>
                        <td>{{ $rawMaterial->id }}</td>
                        <td>{{ $rawMaterial->name }}</td>
                        <td class="btn-group">
                            <a href="{{ route('raw_materials.edit', $rawMaterial->id) }}" class="btn btn-warning">Edit</a>
                            <form action="{{ route('raw_materials.destroy', $rawMaterial->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
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