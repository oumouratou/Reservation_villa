<?php

namespace Database\Seeders;

use App\Models\{User, Villa, Amenity, Tag, Reservation, Review, Payment, Availability};
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Amenities ──────────────────────────────────
        $amenities = [
            ['name' => 'WiFi',              'icon' => 'wifi',        'category' => 'basic'],
            ['name' => 'Climatisation',     'icon' => 'ac',          'category' => 'comfort'],
            ['name' => 'Piscine',           'icon' => 'pool',        'category' => 'outdoor'],
            ['name' => 'Parking',           'icon' => 'parking',     'category' => 'basic'],
            ['name' => 'Cuisine équipée',   'icon' => 'kitchen',     'category' => 'basic'],
            ['name' => 'Terrasse',          'icon' => 'terrace',     'category' => 'outdoor'],
            ['name' => 'Barbecue',          'icon' => 'bbq',         'category' => 'outdoor'],
            ['name' => 'Jacuzzi',           'icon' => 'hot-tub',     'category' => 'comfort'],
            ['name' => 'Vue mer',           'icon' => 'sea-view',    'category' => 'outdoor'],
            ['name' => 'Alarme',            'icon' => 'alarm',       'category' => 'security'],
            ['name' => 'Sèche-linge',       'icon' => 'dryer',       'category' => 'basic'],
            ['name' => 'Lave-linge',        'icon' => 'washer',      'category' => 'basic'],
            ['name' => 'TV satellite',      'icon' => 'tv',          'category' => 'comfort'],
            ['name' => 'Salle de sport',    'icon' => 'gym',         'category' => 'comfort'],
            ['name' => 'Animaux acceptés',  'icon' => 'pet',         'category' => 'basic'],
        ];
        foreach ($amenities as $a) Amenity::firstOrCreate(['name' => $a['name']], $a);

        // ── Tags ───────────────────────────────────────
        $tags = ['Vue mer', 'Montagne', 'Campagne', 'Luxe', 'Famille', 'Romantique', 'Surf', 'Ski', 'Centre-ville'];
        foreach ($tags as $t) Tag::firstOrCreate(['name' => $t], ['slug' => \Illuminate\Support\Str::slug($t)]);

        // ── Admin ──────────────────────────────────────
        $admin = User::firstOrCreate(['email' => 'admin@villareserv.com'], [
            'name'              => 'Admin VillaReserv',
            'password'          => Hash::make('Admin@12345'),
            'role'              => 'admin',
            'email_verified_at' => now(),
            'is_active'         => true,
        ]);

        // ── Owners ─────────────────────────────────────
        $owners = [];
        for ($i = 1; $i <= 3; $i++) {
            $owners[] = User::firstOrCreate(['email' => "owner{$i}@villareserv.com"], [
                'name'              => "Propriétaire {$i}",
                'password'          => Hash::make('Owner@12345'),
                'role'              => 'owner',
                'email_verified_at' => now(),
                'is_active'         => true,
                'phone'             => '+33 6 00 00 000' . $i,
            ]);
        }

        // ── Travelers ──────────────────────────────────
        $travelers = [];
        for ($i = 1; $i <= 5; $i++) {
            $travelers[] = User::firstOrCreate(['email' => "traveler{$i}@villareserv.com"], [
                'name'              => "Voyageur {$i}",
                'password'          => Hash::make('Travel@12345'),
                'role'              => 'traveler',
                'email_verified_at' => now(),
                'is_active'         => true,
            ]);
        }

        // ── Villas ─────────────────────────────────────
        $villaData = [
            ['title' => 'Villa Azur - Vue mer exceptionnelle', 'city' => 'Nice', 'country' => 'France', 'price' => 350, 'beds' => 4, 'baths' => 3, 'guests' => 8, 'lat' => 43.71, 'lng' => 7.26],
            ['title' => 'Mas Provençal avec piscine', 'city' => 'Aix-en-Provence', 'country' => 'France', 'price' => 220, 'beds' => 3, 'baths' => 2, 'guests' => 6, 'lat' => 43.52, 'lng' => 5.44],
            ['title' => 'Villa Toscane - Coeur de vignoble', 'city' => 'Florence', 'country' => 'Italie', 'price' => 280, 'beds' => 5, 'baths' => 4, 'guests' => 10, 'lat' => 43.76, 'lng' => 11.25],
            ['title' => 'Riad de luxe avec spa', 'city' => 'Marrakech', 'country' => 'Maroc', 'price' => 180, 'beds' => 3, 'baths' => 3, 'guests' => 6, 'lat' => 31.63, 'lng' => -7.98],
            ['title' => 'Villa Santorini - Caldera view', 'city' => 'Oia', 'country' => 'Grèce', 'price' => 450, 'beds' => 2, 'baths' => 2, 'guests' => 4, 'lat' => 36.46, 'lng' => 25.37],
        ];

        $amenityIds = Amenity::pluck('id')->toArray();
        $tagIds     = Tag::pluck('id')->toArray();

        foreach ($villaData as $i => $v) {
            $owner = $owners[$i % count($owners)];
            $villa = Villa::firstOrCreate(['slug' => \Illuminate\Support\Str::slug($v['title'])], [
                'owner_id'            => $owner->id,
                'title'               => $v['title'],
                'description'         => 'Magnifique villa offrant un cadre exceptionnel pour vos vacances. Entièrement équipée, elle vous accueille dans un confort absolu avec toutes les commodités modernes. Idéale pour un séjour en famille ou entre amis.',
                'address'             => '123 Chemin des Villas',
                'city'                => $v['city'],
                'country'             => $v['country'],
                'latitude'            => $v['lat'],
                'longitude'           => $v['lng'],
                'price_per_night'     => $v['price'],
                'weekend_price'       => $v['price'] * 1.2,
                'cleaning_fee'        => 80,
                'security_deposit'    => $v['price'] * 2,
                'max_guests'          => $v['guests'],
                'bedrooms'            => $v['beds'],
                'bathrooms'           => $v['baths'],
                'status'              => 'approved',
                'cancellation_policy' => 'moderate',
                'min_nights'          => 2,
                'rules'               => 'Non-fumeur. Animaux acceptés avec accord. Respect du voisinage après 22h.',
                'rating'              => rand(42, 50) / 10,
                'reviews_count'       => rand(5, 30),
            ]);

            $villa->amenities()->sync(array_slice($amenityIds, 0, rand(5, 10)));
            $villa->tags()->sync(array_slice($tagIds, $i % 3, 3));
        }

        $this->command->info('✅ Base de données initialisée avec succès !');
        $this->command->info('Admin: admin@villareserv.com / Admin@12345');
        $this->command->info('Owner: owner1@villareserv.com / Owner@12345');
        $this->command->info('Traveler: traveler1@villareserv.com / Travel@12345');
    }
}