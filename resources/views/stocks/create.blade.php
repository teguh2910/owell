<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Stok Raw Material</title>
    <style>
        body { font-family: sans-serif; margin: 20px; }
        form { margin-top: 20px; }
        div { margin-bottom: 10px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="number"], input[type="text"], textarea, select { width: 300px; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        button { padding: 10px 15px; background-color: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background-color: #218838; }
        .alert-danger { color: #721c24; background-color: #f8d7da; border-color: #f5c6cb; padding: 10px; margin-bottom: 10px; border: 1px solid; border-radius: 4px; }
    </style>
</head>
<body>
    <h1>Tambah Stok Raw Material</h1>

    @if ($errors->any())
        <div class="alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('stocks.store') }}" method="POST">
        @csrf
        <div>
            <label for="raw_material_id">Raw Material:</label>
            @if ($rawMaterials->isEmpty())
                <p>Tidak ada Raw Material yang belum memiliki entri stok. Harap tambahkan Raw Material terlebih dahulu atau edit stok yang sudah ada.</p>
                <a href="{{ route('raw_materials.create') }}" class="btn btn-primary">Tambah Raw Material Baru</a>
            @else
                <select id="raw_material_id" name="raw_material_id" required>
                    <option value="">-- Pilih Raw Material --</option>
                    @foreach ($rawMaterials as $rawMaterial)
                        <option value="{{ $rawMaterial->id }}" {{ old('raw_material_id') == $rawMaterial->id ? 'selected' : '' }}>
                            {{ $rawMaterial->name }}
                        </option>
                    @endforeach
                </select>
            @endif
        </div>

        @if (!$rawMaterials->isEmpty())
            <div>
                <label for="ready_stock">Stok Ready:</label>
                <input type="number" id="ready_stock" name="ready_stock" value="{{ old('ready_stock', 0) }}" min="0" required>
            </div>
            <div>
                <label for="in_process_stock">Stok Dalam Proses:</label>
                <input type="number" id="in_process_stock" name="in_process_stock" value="{{ old('in_process_stock', 0) }}" min="0" required>
            </div>
            <div>
                <label for="process_status">Keterangan Proses (Opsional):</label>
                <input type="text" id="process_status" name="process_status" value="{{ old('process_status') }}">
            </div>
            <button type="submit">Simpan Stok</button>
        @endif
        <a href="{{ route('stocks.index') }}" style="margin-left: 10px;">Kembali ke Daftar</a>
    </form>
</body>
</html>