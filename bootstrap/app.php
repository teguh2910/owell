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
        // --- Tambahkan definisi scheduler di sini ---
        // Ini adalah Schedule callback untuk Laravel 11/12
        schedule: function (Schedule $schedule) {
            // Jadwalkan reminder stok kritis setiap hari jam 8 pagi
            // Ganti 'YOUR_GROUP_JID' dengan ID JID grup WhatsApp Anda
            // Pastikan Anda mendapatkan JID grup yang benar!
            $schedule->command('reminder:critical-stock --group-id="YOUR_GROUP_JID"')->dailyAt('08:00');
        }
        // ------------------------------------------
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
