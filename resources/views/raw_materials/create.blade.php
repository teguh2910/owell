<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Raw Material</title>
    <style>
        body { font-family: sans-serif; margin: 20px; }
        form { margin-top: 20px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"] { width: 300px; padding: 8px; margin-bottom: 10px; border: 1px solid #ddd; border-radius: 4px; }
        button { padding: 10px 15px; background-color: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background-color: #218838; }
        .alert-danger { color: #721c24; background-color: #f8d7da; border-color: #f5c6cb; padding: 10px; margin-bottom: 10px; border: 1px solid; border-radius: 4px; }
    </style>
</head>
<body>
    <h1>Tambah Raw Material Baru</h1>

    @if ($errors->any())
        <div class="alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('raw_materials.store') }}" method="POST">
        @csrf
        <div>
            <label for="name">Nama Raw Material:</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" required>
        </div>
        <button type="submit">Simpan</button>
        <a href="{{ route('raw_materials.index') }}" style="margin-left: 10px;">Kembali ke Daftar</a>
    </form>
</body>
</html>