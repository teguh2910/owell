<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RawMaterial;
use App\Models\Stock;

class ChatbotController extends Controller
{
    /**
     * Handle stock inquiry from chatbot.
     * Expected JSON payload: { "raw_material_name": "Kain Katun" }
     */
    public function getStockStatus(Request $request)
    {
        $materialName = $request->input('raw_material_name');

        if (empty($materialName)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Mohon berikan nama material yang ingin Anda tanyakan stoknya.'
            ], 400);
        }

        // Cari raw material berdasarkan nama
        $rawMaterial = RawMaterial::where('name', 'LIKE', '%' . $materialName . '%')->first();

        if (!$rawMaterial) {
            return response()->json([
                'status' => 'success',
                'answer' => "Maaf, material '{$materialName}' tidak ditemukan di daftar kami. Pastikan namanya sudah benar."
            ]);
        }

        // Ambil data stok untuk material tersebut
        $stock = Stock::where('raw_material_id', $rawMaterial->id)->first();

        if (!$stock) {
            return response()->json([
                'status' => 'success',
                'answer' => "Stok untuk material '{$rawMaterial->name}' belum tercatat. Mohon hubungi administrator."
            ]);
        }

        // Bentuk jawaban
        $answer = "Stok untuk material '{$rawMaterial->name}':\n";
        $answer .= "- Stok Ready: {$stock->ready_stock}\n";
        $answer .= "- Stok Dalam Proses: {$stock->in_process_stock}\n";

        if (!empty($stock->process_status)) {
            $answer .= "- Keterangan Proses: {$stock->process_status}\n";
        }

        if ($stock->estimated_depletion_date) {
            $answer .= "- Estimasi Habis: {$stock->estimated_depletion_date->format('d M Y')}\n";
        } else {
            $answer .= "- Estimasi Habis: Aman / Belum ada kebutuhan\n";
        }

        if ($stock->is_critical) {
            $answer .= "⚠️ Peringatan: Stok ini dalam kondisi KRITIS!";
        }

        return response()->json([
            'status' => 'success',
            'answer' => $answer
        ]);
    }
}