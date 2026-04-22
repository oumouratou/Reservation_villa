<?php

namespace Database\Seeders;

use App\Models\Payment;
use App\Models\Reservation;
use App\Models\User;
use App\Models\Villa;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $owner = User::firstOrCreate(
            ['email' => 'owner.demo@example.com'],
            [
                'name' => 'Owner Demo',
                'password' => Hash::make('Password123'),
                'role' => 'owner',
            ]
        );

        $traveler = User::firstOrCreate(
            ['email' => 'traveler.demo@example.com'],
            [
                'name' => 'Traveler Demo',
                'password' => Hash::make('Password123'),
                'role' => 'traveler',
            ]
        );

        $villa = Villa::firstOrCreate(
            ['slug' => 'villa-demo-001'],
            [
                'owner_id' => $owner->id,
                'title' => 'Villa Demo',
                'description' => 'Villa de demonstration',
                'address' => '1 rue de la Plage',
                'city' => 'Dakar',
                'country' => 'Senegal',
                'latitude' => 14.7167,
                'longitude' => -17.4677,
                'price_per_night' => 250,
                'weekend_price' => 300,
                'cleaning_fee' => 20,
                'security_deposit' => 100,
                'max_guests' => 6,
                'bedrooms' => 3,
                'bathrooms' => 2,
                'status' => 'approved',
                'cancellation_policy' => 'moderate',
                'min_nights' => 2,
                'rating' => 4.5,
                'reviews_count' => 1,
            ]
        );

        $reservation = Reservation::firstOrCreate(
            [
                'villa_id' => $villa->id,
                'traveler_id' => $traveler->id,
                'check_in' => now()->addDays(10)->toDateString(),
                'check_out' => now()->addDays(15)->toDateString(),
            ],
            [
                'guests' => 2,
                'nights' => 5,
                'base_price' => 1250,
                'cleaning_fee' => 20,
                'service_fee' => 125,
                'security_deposit' => 100,
                'total_price' => 1495,
                'status' => 'approved',
                'payment_status' => 'paid',
            ]
        );

        Payment::firstOrCreate(
            ['reservation_id' => $reservation->id],
            [
                'traveler_id' => $traveler->id,
                'amount' => 1495,
                'currency' => 'eur',
                'method' => 'stripe',
                'status' => 'completed',
            ]
        );
    }
}
