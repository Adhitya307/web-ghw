<?php

namespace Config;

use CodeIgniter\Events\Events;
use CodeIgniter\Exceptions\FrameworkException;
use CodeIgniter\HotReloader\HotReloader;
use App\Controllers\Rembesan\SRController;

/*
 * --------------------------------------------------------------------
 * Application Events
 * --------------------------------------------------------------------
 * Events allow you to tap into the execution of the program without
 * modifying or extending core files. This file provides a central
 * location to define your events, though they can always be added
 * at run-time if needed.
 *
 * Example:
 *      Events::on('create', [$myInstance, 'myMethod']);
 */

Events::on('pre_system', static function (): void {
    if (ENVIRONMENT !== 'testing') {
        if (ini_get('zlib.output_compression')) {
            throw FrameworkException::forEnabledZlibOutputCompression();
        }

        while (ob_get_level() > 0) {
            ob_end_flush();
        }

        ob_start(static fn ($buffer) => $buffer);
    }

    /*
     * --------------------------------------------------------------------
     * Debug Toolbar Listeners
     * --------------------------------------------------------------------
     * If you delete, they will no longer be collected.
     */
    if (CI_DEBUG && ! is_cli()) {
        Events::on('DBQuery', 'CodeIgniter\Debug\Toolbar\Collectors\Database::collect');
        service('toolbar')->respond();

        // Hot Reload route - for framework use on the hot reloader.
        if (ENVIRONMENT === 'development') {
            service('routes')->get('__hot-reload', static function (): void {
                (new HotReloader())->run();
            });
        }
    }
});

/*
 * --------------------------------------------------------------------
 * Custom Application Events
 * --------------------------------------------------------------------
 * Tambahkan semua event yang ingin dijalankan saat runtime di sini
 */

// Config/Events.php

Events::on('dataThomson:insert', function($pengukuran_id) {
    log_message('debug', "🎯 Event dataThomson:insert triggered for ID: {$pengukuran_id}");
    
    try {
        // Gunakan service locator untuk menghindari dependency issues
        $rumusController = new \App\Controllers\Rembesan\RumusRembesan();
        
        // Pastikan controller berhasil dibuat
        if (!$rumusController) {
            throw new \Exception("Failed to instantiate RumusRembesan controller");
        }
        
        $result = $rumusController->inputDataForId($pengukuran_id);
        
        if ($result && isset($result['success']) && $result['success']) {
            log_message('debug', "✅ Perhitungan Thomson completed for ID: {$pengukuran_id}");
        } else {
            log_message('error', "❌ Perhitungan Thomson failed for ID: {$pengukuran_id}");
        }
    } catch (\Exception $e) {
        log_message('error', "🔥 Error in dataThomson:insert event: " . $e->getMessage());
        log_message('error', "Stack trace: " . $e->getTraceAsString());
    }
});

// 🔹 Listener untuk event dataSR:insert
Events::on('dataSR:insert', function($pengukuran_id) {
    log_message('debug', "[Events] Trigger dataSR:insert untuk ID: {$pengukuran_id}");
    try {
        $srCtrl = new SRController();
        $srCtrl->hitung($pengukuran_id); // jalankan perhitungan SR
        log_message('debug', "[Events] SRController::hitung berhasil untuk ID: {$pengukuran_id}");
    } catch (\Exception $e) {
        log_message('error', "[Events] SRController gagal: " . $e->getMessage());
    }
});