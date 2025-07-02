<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import Data Stok Raw Material</title>
    <style>
        body {
            font-family: sans-serif;
            margin: 20px;
        }

        form {
            margin-top: 20px;
        }

        div {
            margin-bottom: 10px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input[type="file"] {
            border: 1px solid #ddd;
            padding: 8px;
            border-radius: 4px;
        }

        button {
            padding: 10px 15px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #218838;
        }

        .alert-danger {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid;
            border-radius: 4px;
        }

        .alert-error {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid;
            border-radius: 4px;
        }

        ul {
            margin-top: 0;
            padding-left: 20px;
        }
    </style>
</head>

<body>
    <h1>Import Data Stok Raw Material dari Excel</h1>

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

    <form action="{{ route('stocks.import') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div>
            <label for="file">Pilih File Excel (.xlsx, .xls):</label>
            <input type="file" id="file" name="file" accept=".xlsx, .xls" required>
        </div>
        <button type="submit">Import Data</button>
        <a href="{{ route('stocks.index') }}" style="margin-left: 10px;">Kembali ke Daftar Stok</a>
    </form>

    <h3 style="margin-top: 30px;">Format File Excel:</h3>
    <p>Pastikan file Excel Anda memiliki kolom dengan nama persis seperti di bawah (case-insensitive):</p>
    <ul>
        <li>`nama_raw_material` (harus sesuai dengan nama di Master Data Raw Material)</li>
        <li>`ready_stock` (jumlah integer, contoh: 500)</li>
        <li>`in_process_stock` (jumlah integer, contoh: 200)</li>
        <li>`process_status` (teks, opsional, contoh: "Dalam Pengiriman")</li>
        <li>`expired_date` (tanggal dalam format YYYY-MM-DD atau Excel Serial Number, opsional, contoh: 2025-12-31)</li>
    </ul>
    <p>Contoh isi file Excel (tanpa kolom ID, hanya header baris pertama):</p>
    <pre>
| nama_raw_material | ready_stock | in_process_stock | process_status     | expired_date |
|-------------------|-------------|------------------|--------------------|--------------|
| ZZY               | 500         | 100              | CA1                | 2026-06-30   |
| WBY               | 200         | 50               | CA2                |              |
| 1D6               | 1000        | 0                | Filling            | 2025-12-31   |
    </pre>
</body>

</html>
