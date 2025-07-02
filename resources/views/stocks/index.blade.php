@extends('layouts.app')

@section('title', 'Manajemen Stok Raw Material')

@section('content')
    <h1>Manajemen Stok Raw Material</h1>

    <div style="margin-bottom: 20px;">
        <a href="{{ route('stocks.create') }}" class="btn btn-primary">Tambah Stok Baru</a>
        <a href="{{ route('stocks.import.form') }}" class="btn btn-primary"
            style="background-color: #17a2b8; margin-left: 10px;">Import dari Excel</a>
        <form action="{{ route('stocks.refresh.all') }}" method="POST" style="display: inline-block; margin-left: 10px;">
            @csrf
            <button type="submit" class="btn btn-primary" style="background-color: #6c757d;">Refresh Semua Status
                Stok</button>
        </form>
    </div>

    @if ($stocks->isEmpty())
        <p>Belum ada data stok raw material.</p>
    @else
        <table id="stocksTable"> {{-- Tambahkan ID ini --}}
            <thead>
                <tr>
                    <th>Raw Material</th>
                    <th>Stok Ready</th>
                    <th>Stok Dalam Proses</th>
                    <th>Status Proses</th>
                    <th>Estimasi Habis</th>
                    <th>Kedaluwarsa</th>
                    <th>Stok AiiA</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($stocks as $stock)
                    <tr class="{{ $stock->is_critical ? 'critical-stock' : '' }}">
                        <td>{{ $stock->rawMaterial->name }}</td>
                        <td>{{ $stock->ready_stock }}</td>
                        <td>{{ $stock->in_process_stock }}</td>
                        <td>{{ $stock->process_status ?? '-' }}</td>
                        <td>
                            @if ($stock->estimated_depletion_date)
                                {{ $stock->estimated_depletion_date->format('d M Y') }}
                            @else
                                Aman / Belum ada kebutuhan
                            @endif
                        </td>
                        <td>
                            @if ($stock->expired_date)
                                {{ $stock->expired_date->format('d M Y') }}
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ $stock->aiia_stock }}</td>
                        <td class="btn-group" width="90px">
                            {{-- Ikon Detail --}}
                            {{-- Ikon Edit/Update --}}
                            <a href="{{ route('stocks.edit', $stock->id) }}" class="btn btn-warning"
                                title="Edit/Update">✏️</a>
                            {{-- Ikon Hapus --}}
                            <form action="{{ route('stocks.destroy', $stock->id) }}" method="POST"
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
    <script>
        $(document).ready(function() {
            $('#stocksTable').DataTable({
                // Kolom Estimasi Habis berada pada indeks ke-5 (0-indexed)
                // Kolom: 0:ID, 1:Raw Material, 2:Stok Ready, 3:Stok Dalam Proses, 4:Status Proses, 5:Estimasi Habis, 6:Kedaluwarsa, 7:Aksi
                "order": []
            });
        });
    </script>
@endpush
