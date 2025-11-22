<?php
if (!function_exists('formatNumber')) {
    function formatNumber($value, $decimal = 4) {
        if ($value === null || $value === '' || $value === '-') {
            return '-';
        }
        
        if (is_numeric($value)) {
            return number_format((float)$value, $decimal, '.', '');
        }
        
        return $value;
    }
}