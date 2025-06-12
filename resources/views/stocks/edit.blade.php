<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Stok Raw Material</title>
    <style>
        body { font-family: sans-serif; margin: 20px; }
        form { margin-top: 20px; }
        div { margin-bottom: 10px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="number"], input[type="text"], textarea { width: 300px; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        button { padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; }
        .btn-update { background-color: #007bff; color: white; }
        .btn-update:hover { background-color: #0056b3; }
        .alert-danger { color: #721c24; background-color: #f8d7da; border-color: #f5c6cb; padding: 10px; margin-bottom: 10px; border: 1px solid; border-radius: 4px; }
    </style>
</head>
<body>
    <h1>Edit Stok Raw Material: {{ $stock->rawMaterial->name }}</h1>

    @if ($errors->any())
        <div class="alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('stocks.update', $stock->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div>
            <label for="ready_stock">Stok Ready:</label>
            <input type="number" id="ready_stock" name="ready_stock" value="{{ old('ready_stock', $stock->ready_stock) }}" min="0" required>
        </div>
        <div>
            <label for="in_process_stock">Stok Dalam Proses:</label>
            <input type="number" id="in_process_stock" name="in_process_stock" value="{{ old('in_process_stock', $stock->in_process_stock) }}" min="0" required>
        </div>
        <div>
            <label for="process_status">Keterangan Proses (Opsional):</label>
            <input type="text" id="process_status" name="process_status" value="{{ old('process_status', $stock->process_status) }}">
        </div>
        <button type="submit" class="btn-update">Update Stok</button>
        <a href="{{ route('stocks.index') }}" style="margin-left: 10px;">Kembali ke Daftar</a>
    </form>
</body>
</html>