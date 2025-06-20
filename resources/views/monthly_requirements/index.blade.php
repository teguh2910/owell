@extends('layouts.app')

@section('title', 'Kebutuhan Raw Material Bulanan')

@section('content')
    <h1>Kebutuhan Raw Material Bulanan</h1>

    <div style="margin-bottom: 20px;">
        <a href="{{ route('monthly_requirements.create') }}" class="btn btn-primary">Input Kebutuhan Baru</a>
        <a href="{{ route('monthly_requirements.import.form') }}" class="btn btn-primary"
            style="background-color: #17a2b8; margin-left: 10px;">Import dari Excel</a>
    </div>

    @if ($monthlyRequirements->isEmpty())
        <p>Belum ada data kebutuhan bulanan.</p>
    @else
        <table id="monthlyRequirementsTable"> {{-- Tambahkan ID ini --}}
            <thead>
                <tr>
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
                        <td>{{ $req->rawMaterial->name }}</td>
                        <td>{{ $req->year }}</td>
                        <td>{{ date('F', mktime(0, 0, 0, $req->month, 1)) }}</td>
                        <td>{{ $req->total_monthly_usage }}</td>
                        <td>{{ $req->weekly_usage_1 }}</td>
                        <td>{{ $req->weekly_usage_2 }}</td>
                        <td>{{ $req->weekly_usage_3 }}</td>
                        <td>{{ $req->weekly_usage_4 }}</td>
                        <td class="btn-group" width="90px">
                            {{-- Ikon Edit --}}
                            <a href="{{ route('monthly_requirements.edit', $req->id) }}" class="btn btn-warning"
                                title="Edit">✏️</a>
                            {{-- Ikon Hapus --}}
                            <form action="{{ route('monthly_requirements.destroy', $req->id) }}" method="POST"
                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" title="Hapus">🗑️</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
@endsection

@push('scripts')
    {{-- Tambahkan blok ini --}}
    <script>
        $(document).ready(function() {
            $('#monthlyRequirementsTable').DataTable();
        });
    </script>
@endpush
