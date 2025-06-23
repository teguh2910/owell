<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Stock;
use App\Models\RawMaterial;
// use GuzzleHttp\Client; // Tidak perlu Guzzle lagi
use Carbon\Carbon;
use Symfony\Component\Process\Process; // Import Process
use Symfony\Component\Process\Exception\ProcessFailedException; // Import Exception

class SendCriticalStockReminder extends Command
{
    protected $signature = 'reminder:critical-stock {--group-id= : The WhatsApp Group ID (JID) to send the reminder to (e.g., 1234567890-1234567890@g.us)}';
    protected $description = 'Sends a daily reminder of critical stocks to a specified WhatsApp group by directly executing the Node.js bot script.';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->info('Mencari stok kritis untuk dikirim sebagai reminder...');

        $criticalStocks = Stock::where('is_critical', true)
                                ->with('rawMaterial')
                                ->orderByRaw('CASE WHEN estimated_depletion_date IS NULL THEN 1 ELSE 0 END, estimated_depletion_date ASC')
                                ->get();

        if ($criticalStocks->isEmpty()) {
            $message = "Hebat! Tidak ada material yang dalam kondisi kritis saat ini. Stok aman.";
        } else {
            $message = "🔔 *Pengingat Stok Kritis Hari Ini* 🔔\n\n";
            $message .= "Berikut adalah material yang dalam kondisi KRITIS:\n\n";
            foreach ($criticalStocks as $stock) {
                $message .= "➡️ *{$stock->rawMaterial->name}*\n";
                $message .= "   - Ready: {$stock->ready_stock}\n";
                $message .= "   - Habis: " . ($stock->estimated_depletion_date ? $stock->estimated_depletion_date->format('d M Y') : 'N/A') . "\n";
                if (!empty($stock->process_status)) {
                    $message .= "   - Proses: {$stock->in_process_stock} ({$stock->process_status})\n";
                }
                $message .= "\n";
            }
            $message .= "Segera periksa material-material ini di aplikasi!";
        }

        $groupId = $this->option('group-id');
        if (empty($groupId)) {
            $this->error('Error: WhatsApp Group ID (--group-id) harus ditentukan.');
            return Command::FAILURE;
        }

        // --- Jalankan Script Node.js secara Langsung ---
        // Pastikan path ke script Node.js dan node binary sudah benar
        // Asumsi script Node.js berada di luar folder Laravel (misalnya di /path/to/whatsapp-chatbot-laravel)
        $nodeScriptPath = base_path('../whatsapp-chatbot-laravel/index.js'); // Sesuaikan path ini!
        $nodeBinary = 'node'; // Atau '/usr/bin/node' jika Anda tahu lokasi pastinya

        // Pesan perlu di-escape untuk argumen command line
        // Menggunakan json_encode untuk memastikan string dikutip dengan benar
        $escapedMessage = json_encode($message);

        $command = [$nodeBinary, $nodeScriptPath, 'reminder', $groupId, $escapedMessage];

        $process = new Process($command);
        $process->setTimeout(600); // Set timeout yang lebih lama (misal 10 menit) karena Puppeteer butuh waktu

        try {
            $process->run();

            // Mengecek apakah proses berhasil
            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }

            $this->info('Reminder stok kritis berhasil dikirim ke grup WhatsApp.');
            $this->info('Output Node.js: ' . $process->getOutput());
            $this->error('Error Node.js (jika ada): ' . $process->getErrorOutput());

            return Command::SUCCESS;

        } catch (ProcessFailedException $e) {
            $this->error('Gagal mengirim reminder. Error dari Node.js: ' . $e->getMessage());
            $this->error('Output: ' . $e->getProcess()->getOutput());
            $this->error('Error Output: ' . $e->getProcess()->getErrorOutput());
            return Command::FAILURE;
        } catch (\Exception $e) {
            $this->error('Terjadi kesalahan saat menjalankan bot WhatsApp: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}