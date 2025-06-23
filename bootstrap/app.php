<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule; // <-- PASTIKAN INI DI-IMPORT

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',        
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);
    })
    // --- Tambahkan definisi scheduler di sini ---
    ->withSchedule(function (Schedule $schedule) {
        // Jadwalkan reminder stok kritis setiap hari jam 8 pagi
        // Ganti 'YOUR_GROUP_JID' dengan ID JID grup WhatsApp Anda
        $schedule->command('reminder:critical-stock --group-id="120363401195590086@g.us"')->dailyAt('08:00'); // Gunakan JID yang Anda berikan
    })
    // ------------------------------------------
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
