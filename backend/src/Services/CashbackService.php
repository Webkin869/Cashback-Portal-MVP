<?php
namespace App\Services;

class CashbackService
{
    public function calculate(string $type, float $value, float $orderValue): float
    {
        if ($type === 'fixed') {
            return round($value, 2);
        }
        return round(($orderValue * $value) / 100, 2);
    }
}
