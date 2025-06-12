<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Kebutuhan Bulanan PPIC</title>
    <style>
        body { font-family: sans-serif; margin: 20px; }
        form { margin-top: 20px; }
        div { margin-bottom: 10px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], input[type="number"], select { width: 300px; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        button { padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; }
        .btn-update { background-color: #007bff; color: white; }
        .btn-update:hover { background-color: #0056b3; }
        .alert-danger { color: #721c24; background-color: #f8d7da; border-color: #f5c6cb; padding: 10px; margin-bottom: 10px; border: 1px solid; border-radius: 4px; }
    </style>
</head>
<body>
    <h1>Edit Kebutuhan Bulanan PPIC</h1>

    @if ($errors->any())
        <div class="alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    {{-- Menampilkan error kustom dari controller --}}
    @if (session('errors'))
        <div class="alert-danger">
            @if(is_string(session('errors')))
                {{ session('errors') }}
            @elseif(session('errors')->has('message'))
                {{ session('errors')->first('message') }}
            @endif
        </div>
    @endif


    <form action="{{ route('monthly_requirements.update', $monthlyRequirement->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div>
            <label for="raw_material_id">Raw Material:</label>
            <select id="raw_material_id" name="raw_material_id" required>
                <option value="">-- Pilih Raw Material --</option>
                @foreach ($rawMaterials as $rawMaterial)
                    <option value="{{ $rawMaterial->id }}" {{ old('raw_material_id', $monthlyRequirement->raw_material_id) == $rawMaterial->id ? 'selected' : '' }}>
                        {{ $rawMaterial->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="year">Tahun:</label>
            <input type="number" id="year" name="year" value="{{ old('year', $monthlyRequirement->year) }}" min="{{ date('Y') }}" required>
        </div>
        <div>
            <label for="month">Bulan:</label>
            <select id="month" name="month" required>
                @for ($i = 1; $i <= 12; $i++)
                    <option value="{{ $i }}" {{ old('month', $monthlyRequirement->month) == $i ? 'selected' : '' }}>
                        {{ date("F", mktime(0, 0, 0, $i, 1)) }}
                    </option>
                @endfor
            </select>
        </div>
        <div>
            <label for="total_monthly_usage">Total Kebutuhan Bulanan:</label>
            <input type="number" id="total_monthly_usage" name="total_monthly_usage" value="{{ old('total_monthly_usage', $monthlyRequirement->total_monthly_usage) }}" min="1" required>
        </div>
        <button type="submit" class="btn-update">Update Kebutuhan</button>
        <a href="{{ route('monthly_requirements.index') }}" style="margin-left: 10px;">Kembali ke Daftar</a>
    </form>
</body>
</html>