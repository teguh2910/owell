@extends('layouts.app')

@section('title', 'Dashboard Aplikasi Inventaris')

@section('content')
    <h2>Dashboard Aplikasi Inventaris</h2>
    <p>Selamat datang, {{ Auth::user()->name }} (Role: {{ Auth::user()->role }}).</p>

    <div style="display: flex; flex-wrap: wrap; gap: 20px; margin-top: 30px;">
        {{-- Card Ringkasan Umum --}}
        <div
            style="background-color: #e0f7fa; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); flex: 1 1 calc(33% - 20px);">
            <h3>Total Material</h3>
            <p style="font-size: 2em; font-weight: bold;">{{ $totalRawMaterials }} Raw Material</p>
        </div>
        <div
            style="background-color: #e8f5e9; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); flex: 1 1 calc(33% - 20px);">
            <h3>Stok Ready</h3>
            <p style="font-size: 2em; font-weight: bold;">{{ $totalStockReady }} Kg</p>
        </div>
        <div
            style="background-color: #ffe0b2; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); flex: 1 1 calc(33% - 20px);">
            <h3>Stok Dalam Proses</h3>
            <p style="font-size: 2em; font-weight: bold;">{{ $totalStockInProcess }} Kg</p>
        </div>


        {{-- Card Stok Kritis --}}
        <div
            style="background-color: #ffcdd2; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); flex: 1 1 calc(33% - 20px);">
            <h3>Material Kritis</h3>
            <p style="font-size: 2em; font-weight: bold; color: #d32f2f;">{{ $criticalStocksCount }} Raw Material</p>
            @if ($criticalStocksCount > 0)
                {{-- Hapus kondisi role --}}
                <a href="{{ route('stocks.index') }}" class="btn btn-danger"
                    style="display: block; text-align: center; margin-top: 10px;">Lihat Detail</a>
            @endif
        </div>

        {{-- Card Akan Kedaluwarsa --}}
        <div
            style="background-color: #fff9c4; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); flex: 1 1 calc(33% - 20px);">
            <h3>Akan Kedaluwarsa (30 hari)</h3>
            <p style="font-size: 2em; font-weight: bold; color: #fbc02d;">{{ $expiringSoonCount }} Raw Material</p>
            @if ($expiringSoonCount > 0) {{-- Hapus kondisi role --}}
                <ul style="list-style: none; padding: 0; margin-top: 10px;">
                    @foreach ($expiringSoonList->take(3) as $stock)
                        <li>{{ $stock->rawMaterial->name }} ({{ $stock->expired_date->format('d M Y') }})</li>
                    @endforeach
                    @if ($expiringSoonList->count() > 3)
                        <li><small>+{{ $expiringSoonList->count() - 3 }} lainnya</small></li>
                    @endif
                </ul>
            @endif
        </div>
    </div>

    {{-- Detail Stok Kritis (Sekarang untuk Semua Role) --}}
    <div style="margin-top: 40px;">
        <h3>Daftar Material Kritis:</h3>
        @if ($criticalStocksList->isEmpty())
            <p>Tidak ada material yang dalam kondisi kritis saat ini. Hebat!</p>
        @else
            <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
                <thead>
                    <tr>
                        <th>Raw Material</th>
                        <th>Ready (Kg)</th>
                        <th>Proses (Kg)</th>
                        <th>Estimasi Habis</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($criticalStocksList as $stock)
                        <tr>
                            <td>{{ $stock->rawMaterial->name }}</td>
                            <td>{{ $stock->ready_stock }}</td>
                            <td>{{ $stock->in_process_stock }}</td>
                            <td>{{ $stock->estimated_depletion_date ? $stock->estimated_depletion_date->format('d M Y') : 'N/A' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    {{-- Statistik Kebutuhan Rata-rata (Sekarang untuk Semua Role) --}}
    <div style="margin-top: 40px;">
        <h3>Top 5 Material Rata-rata Kebutuhan Tertinggi:</h3>
        @if ($topMonthlyUsages->isEmpty())
            <p>Belum ada data kebutuhan bulanan.</p>
        @else
            <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
                <thead>
                    <tr>
                        <th>Raw Material</th>
                        <th>Rata-rata Kebutuhan (Kg)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($topMonthlyUsages as $item)
                        <tr>
                            <td>{{ $item->rawMaterial->name }}</td>
                            <td>{{ round($item->avg_usage, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    {{-- Status Proses Breakdown (Sekarang untuk Semua Role) --}}
    <div style="margin-top: 40px;">
        <h3>Status Proses Breakdown:</h3>
        <ul style="list-style: none; padding: 0;">
            <li>Material dengan Status Kansai: {{ $processStatusBreakdown['Kansai'] }}</li>
            <li>Material dengan Status Owell: {{ $processStatusBreakdown['Owell'] }}</li>
            <li>Material dengan Status QA AiiA: {{ $processStatusBreakdown['QA AiiA'] }}</li>
        </ul>
        <small>Ini menunjukkan jumlah material yang memiliki status di kolom tersebut (tidak kosong).</small>
    </div>

@endsection
