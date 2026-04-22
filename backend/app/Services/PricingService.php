<?php

namespace App\Services;

use App\Models\Villa;
use Carbon\Carbon;

class PricingService
{
    public function calculate(Villa $villa, string $checkIn, string $checkOut): array
    {
        $start = Carbon::parse($checkIn)->startOfDay();
        $end = Carbon::parse($checkOut)->startOfDay();
        $nights = max(1, $start->diffInDays($end));

        $base = 0.0;
        $cursor = $start->copy();
        while ($cursor->lt($end)) {
            $base += $villa->getPriceForDate($cursor);
            $cursor->addDay();
        }

        $cleaning = (float) ($villa->cleaning_fee ?? 0);
        $deposit = (float) ($villa->security_deposit ?? 0);
        $service = round($base * 0.1, 2);
        $total = round($base + $cleaning + $service + $deposit, 2);

        return [
            'nights' => $nights,
            'base_price' => round($base, 2),
            'cleaning_fee' => $cleaning,
            'service_fee' => $service,
            'security_deposit' => $deposit,
            'total' => $total,
        ];
    }
}
