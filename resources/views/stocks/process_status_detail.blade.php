@extends('layouts.app')

@section('title', 'Detail Status Proses - ' . $stock->rawMaterial->name)

@section('content')
    <h1>Detail Status Proses: {{ $stock->rawMaterial->name }}</h1>

    <div style="margin-bottom: 20px;">
        <p><strong>Stok Ready:</strong> {{ $stock->ready_stock }}</p>
        <p><strong>Stok Dalam Proses:</strong> {{ $stock->in_process_stock }}</p>
        <p><strong>Stok AiiA:</strong> {{ $stock->aiia_stock }}</p>
        <p><strong>Estimasi Habis:</strong>
            @if ($stock->estimated_depletion_date)
                {{ $stock->estimated_depletion_date->format('d M Y') }}
            @else
                Aman / Belum ada kebutuhan
            @endif
        </p>
        <p><strong>Kedaluwarsa:</strong>
            @if ($stock->expired_date)
                {{ $stock->expired_date->format('d M Y') }}
            @else
                -
            @endif
        </p>
    </div>

    <form action="{{ route('stocks.update', $stock->id) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- --- TAMBAHKAN HIDDEN INPUTS INI --- --}}
        <input type="hidden" name="ready_stock" value="{{ old('ready_stock', $stock->ready_stock) }}">
        <input type="hidden" name="in_process_stock" value="{{ old('in_process_stock', $stock->in_process_stock) }}">
        <input type="hidden" name="aiia_stock" value="{{ old('aiia_stock', $stock->aiia_stock) }}">
        <input type="hidden" name="expired_date"
            value="{{ old('expired_date', $stock->expired_date ? $stock->expired_date->format('Y-m-d') : '') }}">
        {{-- --------------------------------- --}}

        <h3>Update Status Proses:</h3>

        {{-- Input Status Kansai (Hanya untuk Admin dan Supplier) --}}
        @if (Auth::user()->role == 'admin' || Auth::user()->role == 'supplier')
            <div style="margin-bottom: 15px;">
                <label for="kansai_process_status">Status Proses di Kansai:</label>
                <input type="text" id="kansai_process_status" name="kansai_process_status"
                    value="{{ old('kansai_process_status', $stock->kansai_process_status) }}"
                    style="width: 300px; padding: 8px;">
            </div>
        @endif

        {{-- Input Status Owell (Hanya untuk Admin dan PPIC) --}}
        @if (Auth::user()->role == 'admin' || Auth::user()->role == 'ppic')
            <div style="margin-bottom: 15px;">
                <label for="owell_process_status">Status Proses di Owell:</label>
                <input type="text" id="owell_process_status" name="owell_process_status"
                    value="{{ old('owell_process_status', $stock->owell_process_status) }}"
                    style="width: 300px; padding: 8px;">
            </div>
        @endif

        {{-- Input Status QA AiiA (Hanya untuk Admin) --}}
        @if (Auth::user()->role == 'admin')
            <div style="margin-bottom: 15px;">
                <label for="qa_aiia_process_status">Status Proses di QA AiiA:</label>
                <input type="text" id="qa_aiia_process_status" name="qa_aiia_process_status"
                    value="{{ old('qa_aiia_process_status', $stock->qa_aiia_process_status) }}"
                    style="width: 300px; padding: 8px;">
            </div>
        @endif

        {{-- Tombol Simpan --}}
        <button type="submit" class="btn btn-primary" style="background-color: #28a745;">Simpan Perubahan Status</button>
        <a href="{{ route('stocks.index') }}" style="margin-left: 10px;">Kembali ke Daftar Stok</a>
    </form>
@endsection
