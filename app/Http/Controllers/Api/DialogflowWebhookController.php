<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RawMaterial;
use App\Models\Stock;
use Carbon\Carbon;

class DialogflowWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $intent = $request->json('queryResult.intent.displayName');
        $parameters = $request->json('queryResult.parameters');
        $responseMessage = 'Maaf, saya tidak mengerti pertanyaan Anda.';

        switch ($intent) {
            case 'CheckStockStatus':
                $materialName = $parameters['material_name']; // Nama entity dari Dialogflow
                $responseMessage = $this->getStockStatusLogic($materialName);
                break;
            case 'CheckCriticalStocks':
                $responseMessage = $this->getUrgentStocksLogic();
                break;
            // Tambahkan case untuk intent lain jika ada
            case 'Default Welcome Intent':
                $responseMessage = 'Halo! Saya chatbot SATU AISIN AI. Anda bisa bertanya tentang stok material. Contoh: "stok Kain Katun" atau "stok kritis".';
                break;
            default:
                $responseMessage = 'Saya belum diprogram untuk menjawab pertanyaan itu. Mohon coba yang lain.';
                break;
        }

        // Format respons sesuai format Dialogflow
        return response()->json([
            'fulfillmentText' => $responseMessage,
            'payload' => [
                'facebook' => [ // Opsional: jika integrasi ke Facebook Messenger, dll.
                    'text' => $responseMessage
                ],
                'whatsapp' => [ // Opsional: jika integrasi ke WhatsApp (melalui layanan tertentu)
                    'text' => $responseMessage
                ]
            ]
        ]);
    }

    // --- Logika yang Dicomot dari ChatbotController sebelumnya ---
    protected function getStockStatusLogic($materialName)
    {
        $rawMaterial = RawMaterial::where('name', 'LIKE', '%' . $materialName . '%')->first();

        if (!$rawMaterial) {
            return "Maaf, material '{$materialName}' tidak ditemukan di daftar kami. Pastikan namanya sudah benar.";
        }

        $stock = Stock::where('raw_material_id', $rawMaterial->id)->first();

        if (!$stock) {
            return "Stok untuk material '{$rawMaterial->name}' belum tercatat. Mohon hubungi administrator.";
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

        return $answer;
    }

    protected function getUrgentStocksLogic()
    {
        $criticalStocks = Stock::where('is_critical', true)
                                ->with('rawMaterial')
                                ->orderByRaw('CASE WHEN estimated_depletion_date IS NULL THEN 1 ELSE 0 END, estimated_depletion_date ASC')
                                ->get();

        if ($criticalStocks->isEmpty()) {
            return "Hebat! Tidak ada material yang dalam kondisi kritis saat ini.";
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

        return $answer;
    }
}