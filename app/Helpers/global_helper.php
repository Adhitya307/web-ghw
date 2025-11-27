<?php

if (!function_exists('formatNumber')) {
    function formatNumber($value, $decimals = 2) {
        if ($value === null || $value === '' || $value === '-') {
            return '-';
        }
        
        // Jika string "KERING", return as is
        if (is_string($value) && strtoupper(trim($value)) === 'KERING') {
            return 'KERING';
        }
        
        // Coba konversi ke float
        $floatVal = floatval($value);
        
        // Format angka dengan pemisah ribuan
        return number_format($floatVal, $decimals, '.', '');
    }
}