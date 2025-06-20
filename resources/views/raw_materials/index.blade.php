@extends('layouts.app')

@section('title', 'Master Data Raw Material')

@section('content')
    <h1>Master Data Raw Material</h1>

    <div style="margin-bottom: 20px;">
        <a href="{{ route('raw_materials.create') }}" class="btn btn-primary">Tambah Raw Material Baru</a>
        <a href="{{ route('raw_materials.import.form') }}" class="btn btn-primary"
            style="background-color: #17a2b8; margin-left: 10px;">Import dari Excel</a>
    </div>

    @if ($rawMaterials->isEmpty())
        <p>Belum ada data Raw Material.</p>
    @else
        <table id="rawMaterialsTable"> {{-- Tambahkan ID ini --}}
            <thead>
                <tr>
                    <th>Nama Raw Material</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rawMaterials as $rawMaterial)
                    <tr>
                        <td>{{ $rawMaterial->name }}</td>
                        <td class="btn-group">
                            {{-- Ikon Edit --}}
                            <a href="{{ route('raw_materials.edit', $rawMaterial->id) }}" class="btn btn-warning"
                                title="Edit">✏️</a> {{-- Ikon pensil --}}
                            {{-- Ikon Hapus --}}
                            <form action="{{ route('raw_materials.destroy', $rawMaterial->id) }}" method="POST"
                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" title="Hapus">🗑️</button>
                                {{-- Ikon sampah --}}
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
            $('#rawMaterialsTable').DataTable();
        });
    </script>
@endpush
