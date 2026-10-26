<?php
function validate_positive_number($value, $field_name) {
    if (!is_numeric($value) || $value < 0) {
        return "$field_name tidak boleh bernilai negatif.";
    }
    return null;
}

function validate_sku($sku) {
    if (empty(trim($sku))) {
        return "SKU tidak boleh kosong.";
    }
    return null;
}