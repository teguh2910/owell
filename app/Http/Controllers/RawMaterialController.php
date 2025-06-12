<?php

namespace App\Http\Controllers;

use App\Models\RawMaterial;
use Illuminate\Http\Request;

class RawMaterialController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Mengambil semua data raw material dari database
        $rawMaterials = RawMaterial::all();
        // Menampilkan view 'raw_materials.index' dan mengirimkan data rawMaterials
        return view('raw_materials.index', compact('rawMaterials'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Menampilkan view 'raw_materials.create' yang berisi form untuk tambah data
        return view('raw_materials.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi input dari form
        $request->validate([
            'name' => 'required|string|max:255|unique:raw_materials,name',
        ]);

        // Membuat entri baru di database
        RawMaterial::create($request->all());

        // Redirect kembali ke halaman index dengan pesan sukses
        return redirect()->route('raw_materials.index')->with('success', 'Raw Material berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(RawMaterial $rawMaterial)
    {
        // Untuk menampilkan detail satu raw material (opsional untuk aplikasi ini, tapi disertakan)
        return view('raw_materials.show', compact('rawMaterial'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RawMaterial $rawMaterial)
    {
        // Menampilkan view 'raw_materials.edit' dengan data raw material yang akan diedit
        return view('raw_materials.edit', compact('rawMaterial'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RawMaterial $rawMaterial)
    {
        // Validasi input dari form
        $request->validate([
            'name' => 'required|string|max:255|unique:raw_materials,name,' . $rawMaterial->id,
        ]);

        // Memperbarui data di database
        $rawMaterial->update($request->all());

        // Redirect kembali ke halaman index dengan pesan sukses
        return redirect()->route('raw_materials.index')->with('success', 'Raw Material berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RawMaterial $rawMaterial)
    {
        // Menghapus data dari database
        $rawMaterial->delete();

        // Redirect kembali ke halaman index dengan pesan sukses
        return redirect()->route('raw_materials.index')->with('success', 'Raw Material berhasil dihapus!');
    }
}