<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RawMaterial;
use App\Models\Stock;
use Carbon\Carbon; // Pastikan ini di-import jika belum

class ChatbotController extends Controller
{
    /**
     * Handle stock inquiry for a specific raw material.
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

        $rawMaterial = RawMaterial::where('name', 'LIKE', '%' . $materialName . '%')->first();

        if (!$rawMaterial) {
            return response()->json([
                'status' => 'success',
                'answer' => "Maaf, material '{$materialName}' tidak ditemukan di daftar kami. Pastikan namanya sudah benar."
            ]);
        }

        $stock = Stock::where('raw_material_id', $rawMaterial->id)->first();

        if (!$stock) {
            return response()->json([
                'status' => 'success',
                'answer' => "Stok untuk material '{$rawMaterial->name}' belum tercatat. Mohon hubungi administrator."
            ]);
        }

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

    /**
     * Handle inquiry for all critical stocks.
     */
    public function getUrgentStocks(Request $request)
    {
        // Ambil semua stok yang ditandai kritis, urutkan berdasarkan estimasi habis tercepat
        $criticalStocks = Stock::where('is_critical', true)
                                ->with('rawMaterial') // Ambil juga informasi raw materialnya
                                ->orderByRaw('CASE WHEN estimated_depletion_date IS NULL THEN 1 ELSE 0 END, estimated_depletion_date ASC')
                                ->get();

        if ($criticalStocks->isEmpty()) {
            return response()->json([
                'status' => 'success',
                'answer' => "Hebat! Tidak ada material yang dalam kondisi kritis saat ini."
            ]);
        }

        $answer = "Material yang dalam kondisi KRITIS:\n\n";
        foreach ($criticalStocks as $stock) {
            $answer .= "➡️ {$stock->rawMaterial->name}\n";
            $answer .= "   - Ready: {$stock->ready_stock}\n";
            $answer .= "   - Habis: " . ($stock->estimated_depletion_date ? $stock->estimated_depletion_date->format('d M Y') : 'N/A') . "\n";
            if (!empty($stock->process_status)) {
                $answer .= "   - Proses: {$stock->in_process_stock} ({$stock->process_status})\n";
            }
            $answer .= "\n";
        }
        $answer .= "Segera periksa material-material ini!";

        return response()->json([
            'status' => 'success',
            'answer' => $answer
        ]);
    }
}