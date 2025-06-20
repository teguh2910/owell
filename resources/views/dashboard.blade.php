@extends('layouts.app') {{-- Menggunakan layout master Anda --}}

@section('title', 'Dashboard') {{-- Judul halaman --}}

@section('content')
    <h2>Selamat Datang di Dashboard Aplikasi Inventaris!</h2>
    <p>Halo, {{ Auth::user()->name }} (Role: {{ Auth::user()->role }}).</p>
    <p>Gunakan navigasi di atas untuk mengakses fitur-fitur aplikasi.</p>

    {{-- Anda bisa menambahkan ringkasan cepat di sini, contoh: --}}
    {{-- <div style="margin-top: 30px;">
        <h3>Ringkasan Cepat:</h3>
        <ul>
            <li>Jumlah Raw Material: {{ \App\Models\RawMaterial::count() }}</li>
            <li>Jumlah Stok Tersedia: {{ \App\Models\Stock::sum('ready_stock') }}</li>
            <li>Stok Kritis Saat Ini: {{ \App\Models\Stock::where('is_critical', true)->count() }}</li>
        </ul>
    </div> --}}
@endsection
