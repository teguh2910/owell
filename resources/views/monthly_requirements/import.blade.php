<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import Kebutuhan Bulanan</title>
    <style>
        body { font-family: sans-serif; margin: 20px; }
        form { margin-top: 20px; }
        div { margin-bottom: 10px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="file"] { border: 1px solid #ddd; padding: 8px; border-radius: 4px; }
        button { padding: 10px 15px; background-color: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background-color: #218838; }
        .alert-danger { color: #721c24; background-color: #f8d7da; border-color: #f5c6cb; padding: 10px; margin-bottom: 10px; border: 1px solid; border-radius: 4px; }
        .alert-error { color: #721c24; background-color: #f8d7da; border-color: #f5c6cb; padding: 10px; margin-bottom: 10px; border: 1px solid; border-radius: 4px; }
        ul { margin-top: 0; padding-left: 20px; }
    </style>
</head>
<body>
    <h1>Import Kebutuhan Bulanan dari Excel</h1>

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
            <strong>Terjadi kesalahan validasi atau impor:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
                @if ($errors->has('excel_errors'))
                    @foreach ($errors->get('excel_errors') as $excelError)
                        <li>{{ $excelError }}</li>
                    @endforeach
                @endif
            </ul>
        </div>
    @endif

    <form action="{{ route('monthly_requirements.import') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div>
            <label for="file">Pilih File Excel (.xlsx, .xls):</label>
            <input type="file" id="file" name="file" accept=".xlsx, .xls" required>
        </div>
        <button type="submit">Import Data</button>
        <a href="{{ route('monthly_requirements.index') }}" style="margin-left: 10px;">Kembali ke Daftar Kebutuhan</a>
    </form>

    <h3 style="margin-top: 30px;">Format File Excel:</h3>
    <p>Pastikan file Excel Anda memiliki kolom dengan nama persis seperti di bawah (case-insensitive):</p>
    <ul>
        <li>`nama_raw_material` (harus sesuai dengan nama di Master Data Raw Material)</li>
        <li>`tahun` (contoh: 2025)</li>
        <li>`bulan` (angka 1-12, contoh: 6 untuk Juni)</li>
        <li>`total_kebutuhan_bulanan` (angka, contoh: 400)</li>
    </ul>
    <p>Contoh isi file Excel (tanpa kolom ID, hanya header baris pertama):</p>
    <pre>
| nama_raw_material | tahun | bulan | total_kebutuhan_bulanan |
|-------------------|-------|-------|-------------------------|
| Kain Katun        | 2025  | 6     | 400                     |
| Benang Polyester  | 2025  | 6     | 150                     |
| Kancing Plastik   | 2025  | 7     | 1000                    |
    </pre>
</body>
</html>