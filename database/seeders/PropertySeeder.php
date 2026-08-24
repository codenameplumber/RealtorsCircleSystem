<?php

namespace Database\Seeders;

use App\Data\NigeriaLocations;
use App\Models\UsersTable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PropertySeeder extends Seeder
{
    private array $stateCities = [
        'Abia'        => 'Umuahia',    'Adamawa'     => 'Yola',
        'Akwa Ibom'   => 'Uyo',        'Anambra'     => 'Awka',
        'Bauchi'      => 'Bauchi',     'Bayelsa'     => 'Yenagoa',
        'Benue'       => 'Makurdi',    'Borno'       => 'Maiduguri',
        'Cross River' => 'Calabar',    'Delta'       => 'Asaba',
        'Ebonyi'      => 'Abakaliki',  'Edo'         => 'Benin City',
        'Ekiti'       => 'Ado Ekiti',  'Enugu'       => 'Enugu',
        'FCT - Abuja' => 'Abuja',      'Gombe'       => 'Gombe',
        'Imo'         => 'Owerri',     'Jigawa'      => 'Dutse',
        'Kaduna'      => 'Kaduna',     'Kano'        => 'Kano',
        'Katsina'     => 'Katsina',    'Kebbi'       => 'Birnin Kebbi',
        'Kogi'        => 'Lokoja',     'Kwara'       => 'Ilorin',
        'Lagos'       => 'Lagos',      'Nasarawa'    => 'Lafia',
        'Niger'       => 'Minna',      'Ogun'        => 'Abeokuta',
        'Ondo'        => 'Akure',      'Osun'        => 'Osogbo',
        'Oyo'         => 'Ibadan',     'Plateau'     => 'Jos',
        'Rivers'      => 'Port Harcourt', 'Sokoto'   => 'Sokoto',
        'Taraba'      => 'Jalingo',    'Yobe'        => 'Damaturu',
        'Zamfara'     => 'Gusau',
    ];

    // Property types cycling across 15 slots
    private array $types = [
        'Apartment', 'Duplex', 'Bungalow', 'Detached House', 'Semi-Detached',
        'Terraced House', 'Apartment', 'Land', 'Commercial', 'Penthouse',
        'Studio', 'Duplex', 'Bungalow', 'Detached House', 'Land',
    ];

    // Listing types cycling across 15 slots
    private array $listingTypes = [
        'For Sale', 'For Rent', 'For Sale', 'Short Let', 'For Sale',
        'For Rent', 'For Sale', 'For Sale', 'For Rent', 'For Sale',
        'Short Let', 'For Sale', 'For Rent', 'For Sale', 'For Sale',
    ];

    private array $streets = [
        '1 Airport Road', '7 Bank Street', '15 Unity Drive', '23 Market Road',
        '4 Hospital Avenue', '11 Independence Way', '8 Government Drive',
        '30 College Road', '17 Church Street', '5 Stadium Road',
        '12 University Avenue', '3 GRA Close', '22 Estate Drive',
        '9 Mission Road', '18 Palm Avenue', '6 Queen Street',
        '14 Railway Road', '27 Industrial Layout', '2 New Layout Road',
        '35 Old Secretariat Road',
    ];

    private array $amenitySets = [
        ['Swimming Pool', 'Generator', 'Security/Guard', 'Parking Space', 'Water Supply'],
        ['Generator', 'CCTV', 'Parking Space', 'Air Conditioning', 'Internet/WiFi'],
        ['Swimming Pool', 'Gym/Fitness Center', 'Generator', 'Security/Guard', 'CCTV', 'Parking Space'],
        ['Generator', 'Water Supply', 'Fence/Wall', 'Boys Quarter', 'Parking Space'],
        ['Solar Power', 'Generator', 'Security/Guard', 'CCTV', 'Parking Space', 'Water Supply'],
        ['Swimming Pool', 'Generator', 'Gym/Fitness Center', 'Air Conditioning', 'Smart Home', 'CCTV'],
        ['Generator', 'Balcony', 'Parking Space', 'Internet/WiFi', 'Water Supply', 'Furnished'],
        ['Generator', 'Security/Guard', 'Boys Quarter', 'Garden/Lawn', 'Fence/Wall', 'Parking Space'],
        ['Air Conditioning', 'CCTV', 'Intercom', 'Elevator', 'Generator', 'Parking Space'],
        ['Solar Power', 'Generator', 'Swimming Pool', 'Garden/Lawn', 'Security/Guard', 'Boys Quarter'],
    ];

    private array $bedroomPrefix = [1, 2, 3, 4, 5, 3, 4, 2, 5, 3, 1, 4, 2, 3, 5];

    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('properties_tables')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $users      = UsersTable::select('id', 'State')->get();
        $stateMap   = NigeriaLocations::statesWithLgas();
        $stateList  = array_keys($stateMap);
        $now        = now()->toDateTimeString();
        $records    = [];
        $photoSeeds = 1;

        foreach ($users as $ui => $user) {
            for ($p = 0; $p < 15; $p++) {
                // Spread properties across all states deterministically
                $stateIndex  = ($ui * 7 + $p * 3) % count($stateList);
                $state       = $stateList[$stateIndex];
                $lgas        = $stateMap[$state];
                $lga         = $lgas[($ui + $p) % count($lgas)];
                $city        = $this->stateCities[$state] ?? $state;
                $type        = $this->types[$p];
                $listingType = $this->listingTypes[$p];
                $beds        = $this->bedroomPrefix[$p];
                $amenities   = $this->amenitySets[$p % count($this->amenitySets)];
                $street      = $this->streets[($ui + $p) % count($this->streets)];
                $price       = $this->generatePrice($type, $listingType);
                $title       = $this->makeTitle($type, $lga, $beds);
                $description = $this->makeDescription($type, $listingType, $state, $lga, $city, $beds);

                $seed1 = $photoSeeds++;
                $seed2 = $photoSeeds++;
                $seed3 = $photoSeeds++;

                $records[] = [
                    'user_id'         => $user->id,
                    'title'           => $title,
                    'type'            => $type,
                    'description'     => $description,
                    'listing_type'    => $listingType,
                    'street'          => $street,
                    'neigborhood'     => $lga,
                    'city'            => $city,
                    'state'           => $state,
                    'localarea'       => $lga,
                    'country'         => 'Nigeria',
                    'amenities'       => json_encode($amenities),
                    'price'           => $price,
                    'photos'          => json_encode([
                        "https://picsum.photos/seed/rc{$seed1}/1200/800",
                        "https://picsum.photos/seed/rc{$seed2}/1200/800",
                        "https://picsum.photos/seed/rc{$seed3}/1200/800",
                    ]),
                    'videos'          => json_encode([]),
                    'listing_status'  => 'active',
                    'approval_status' => 'approved',
                    'views'           => rand(5, 850),
                    'created_at'      => now()->subDays(rand(1, 180))->toDateTimeString(),
                    'updated_at'      => $now,
                ];
            }
        }

        // Bulk insert in chunks of 100 for performance
        $chunks = array_chunk($records, 100);
        foreach ($chunks as $chunk) {
            DB::table('properties_tables')->insert($chunk);
        }

        $this->command->info('✓ ' . count($records) . ' properties seeded across all states.');
    }

    private function makeTitle(string $type, string $lga, int $beds): string
    {
        return match (true) {
            $type === 'Land'   => "{$beds} Plot" . ($beds > 1 ? 's' : '') . " of Land in {$lga}",
            in_array($type, ['Commercial', 'Office Space', 'Warehouse', 'Industrial'])
                               => "{$type} Property in {$lga}",
            $type === 'Studio' => "Studio Apartment in {$lga}",
            default            => "{$beds} Bedroom {$type} in {$lga}",
        };
    }

    private function makeDescription(
        string $type, string $listingType, string $state,
        string $lga, string $city, int $beds
    ): string {
        $templates = [
            "This stunning {$beds}-bedroom {$type} is situated in the heart of {$lga}, {$state}. "
            . "Offering a serene and secure environment, the property features modern architectural "
            . "designs, spacious rooms, and excellent finishes throughout. Located minutes from "
            . "key amenities including schools, hospitals, and shopping centres in {$city}. "
            . "Perfect for families and investors. Available {$listingType}.",

            "Exquisite {$beds}-bedroom {$type} now available {$listingType} in the prestigious "
            . "{$lga} area of {$city}, {$state}. This property boasts well-appointed rooms, "
            . "modern kitchen, contemporary bathrooms, and ample parking. Set in a gated "
            . "estate with 24-hour security, constant water supply, and reliable power backup. "
            . "A rare find at this price — schedule a viewing today.",

            "Beautifully finished {$type} {$listingType} in {$lga}, {$state}. "
            . "Features {$beds} generously sized bedrooms, en-suite bathrooms, and a "
            . "spacious living area perfect for modern family living. The property enjoys "
            . "easy access to the main expressway and is close to top schools, churches, "
            . "mosques, and markets in {$city}. Fully secured with CCTV and perimeter fencing.",

            "Prime {$type} available {$listingType} in {$lga}, {$city}. This {$beds}-bedroom "
            . "property offers premium fixtures, tiled floors, and fitted wardrobes in all rooms. "
            . "The compound includes a boys' quarter, generator house, and a beautifully "
            . "landscaped garden. Situated in one of {$state}'s most desirable neighbourhoods "
            . "with excellent road infrastructure and close proximity to commercial hubs.",

            "Newly built {$beds}-bedroom {$type} strategically located in {$lga}, {$state}. "
            . "The property features an open-plan kitchen and living room concept, floor-to-ceiling "
            . "windows, quality Italian tiles, and fitted kitchen cabinets. Amenities include a "
            . "swimming pool, gym, and dedicated parking. Ideal for professionals and expatriates "
            . "seeking premium accommodation in {$city}.",

            "Tastefully designed {$type} {$listingType} in the thriving community of {$lga}, "
            . "{$state}. With {$beds} bedrooms and {$beds} bathrooms, this home is designed "
            . "for comfort and functionality. The estate provides 24-hour security, underground "
            . "drainage, and wide internal roads. Walking distance to {$city} business district.",

            "Luxurious {$beds}-bedroom {$type} in a serene location within {$lga}, {$city}. "
            . "This magnificent property sits on a large plot with room for expansion. "
            . "Interior highlights include high ceilings, chandelier lighting, imported tiles, "
            . "and smart home features. Don't miss this exceptional {$listingType} opportunity "
            . "in one of {$state}'s most sought-after locations.",
        ];

        // Land description override
        if ($type === 'Land') {
            $templates = [
                "Prime land available {$listingType} in {$lga}, {$state}. The plot is "
                . "survey-document ready, dry land with no government acquisition. Located "
                . "on a tarred road with good drainage. Ideal for residential or commercial "
                . "development. Close to key infrastructure in {$city}.",

                "Strategic {$beds}-plot land parcel situated in {$lga}, {$city}, {$state}. "
                . "The land is fully fenced, gated, and survey-verified. C of O available. "
                . "Excellent access road with utilities connections nearby. Perfect for "
                . "building a home, estate development, or investment purposes.",
            ];
        }

        $index = (strlen($lga) + strlen($type)) % count($templates);
        return $templates[$index];
    }

    private function generatePrice(string $type, string $listingType): string
    {
        $ranges = [
            'Land'         => ['For Sale' => [5_000_000, 120_000_000]],
            'Studio'       => ['For Rent' => [350_000, 900_000], 'Short Let' => [25_000, 80_000], 'For Sale' => [8_000_000, 25_000_000]],
            'Apartment'    => ['For Sale' => [12_000_000, 85_000_000], 'For Rent' => [500_000, 3_500_000], 'Short Let' => [30_000, 150_000]],
            'Duplex'       => ['For Sale' => [35_000_000, 250_000_000], 'For Rent' => [1_200_000, 6_000_000], 'Short Let' => [80_000, 350_000]],
            'Bungalow'     => ['For Sale' => [10_000_000, 60_000_000], 'For Rent' => [400_000, 2_000_000], 'Short Let' => [25_000, 100_000]],
            'Detached House' => ['For Sale' => [45_000_000, 350_000_000], 'For Rent' => [2_000_000, 10_000_000]],
            'Semi-Detached' => ['For Sale' => [25_000_000, 150_000_000], 'For Rent' => [900_000, 4_000_000]],
            'Terraced House' => ['For Sale' => [20_000_000, 100_000_000], 'For Rent' => [700_000, 3_000_000]],
            'Penthouse'    => ['For Sale' => [80_000_000, 500_000_000], 'For Rent' => [3_000_000, 15_000_000], 'Short Let' => [200_000, 800_000]],
            'Commercial'   => ['For Sale' => [30_000_000, 400_000_000], 'For Rent' => [2_000_000, 20_000_000]],
        ];

        $typeRanges = $ranges[$type] ?? $ranges['Apartment'];
        $range = $typeRanges[$listingType] ?? $typeRanges[array_key_first($typeRanges)];

        $price = rand($range[0] / 1_000_000, $range[1] / 1_000_000) * 1_000_000;

        // Format: "45,000,000" or "1,200,000/yr" for rentals
        $formatted = number_format($price);
        if ($listingType === 'For Rent') {
            return $formatted . '/yr';
        }
        if ($listingType === 'Short Let') {
            return $formatted . '/night';
        }
        return $formatted;
    }
}
