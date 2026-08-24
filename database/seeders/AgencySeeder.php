<?php

namespace Database\Seeders;

use App\Models\AgencyPartner;
use App\Models\BankInformation;
use App\Models\UsersTable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AgencySeeder extends Seeder
{
    private array $agencies = [
        ['company' => 'Premier Property Group',      'first' => 'Emeka',     'last' => 'Okafor',       'city' => 'Lagos',        'phone' => '08100000001'],
        ['company' => 'Cityscape Realty Hub',         'first' => 'Ngozi',     'last' => 'Adeleke',      'city' => 'Abuja',        'phone' => '08100000002'],
        ['company' => 'Coastal Homes Network',        'first' => 'Chidi',     'last' => 'Nwosu',        'city' => 'Port Harcourt','phone' => '08100000003'],
        ['company' => 'Heritage Alliance Realty',     'first' => 'Fatima',    'last' => 'Abubakar',     'city' => 'Kano',         'phone' => '08100000004'],
        ['company' => 'Prestige Properties Hub',      'first' => 'Tunde',     'last' => 'Adeyemi',      'city' => 'Ibadan',       'phone' => '08100000005'],
        ['company' => 'TrustMark Realty Services',   'first' => 'Halima',    'last' => 'Bello',        'city' => 'Kaduna',       'phone' => '08100000006'],
        ['company' => 'BroadView Estate Network',     'first' => 'Rotimi',    'last' => 'Bakare',       'city' => 'Benin City',   'phone' => '08100000007'],
        ['company' => 'Sunshine Realty Group',        'first' => 'Amaka',     'last' => 'Eze',          'city' => 'Jos',          'phone' => '08100000008'],
        ['company' => 'NorthStar Property Partners',  'first' => 'Ibrahim',   'last' => 'Yusuf',        'city' => 'Maiduguri',    'phone' => '08100000009'],
        ['company' => 'Summit Estate Services',       'first' => 'Chioma',    'last' => 'Obiora',       'city' => 'Enugu',        'phone' => '08100000010'],
        ['company' => 'Imperial Homes Network',       'first' => 'Uche',      'last' => 'Chukwuemeka',  'city' => 'Onitsha',      'phone' => '08100000011'],
        ['company' => 'Metropolis Property Group',    'first' => 'Blessing',  'last' => 'Okonkwo',      'city' => 'Owerri',       'phone' => '08100000012'],
        ['company' => 'FrontierHomes Alliance',       'first' => 'Yusuf',     'last' => 'Garba',        'city' => 'Warri',        'phone' => '08100000013'],
        ['company' => 'CrossLink Realty Services',    'first' => 'Grace',     'last' => 'Nwachukwu',    'city' => 'Calabar',      'phone' => '08100000014'],
        ['company' => 'BlueSky Property Network',     'first' => 'Femi',      'last' => 'Afolabi',      'city' => 'Asaba',        'phone' => '08100000015'],
        ['company' => 'Crown Realty Alliance',        'first' => 'Zainab',    'last' => 'Mohammed',     'city' => 'Ilorin',       'phone' => '08100000016'],
        ['company' => 'TerraFirma Homes Group',       'first' => 'Kola',      'last' => 'Salami',       'city' => 'Abeokuta',     'phone' => '08100000017'],
        ['company' => 'Vertex Properties Network',    'first' => 'Ifeoma',    'last' => 'Uchenna',      'city' => 'Uyo',          'phone' => '08100000018'],
        ['company' => 'Pinnacle Land & Homes',        'first' => 'Dapo',      'last' => 'Ogundipe',     'city' => 'Akure',        'phone' => '08100000019'],
        ['company' => 'GreenField Realty Circle',     'first' => 'Chinwe',    'last' => 'Danjuma',      'city' => 'Sokoto',       'phone' => '08100000020'],
    ];

    private array $maleFirstNames = [
        'Emeka', 'Tunde', 'Chidi', 'Femi', 'Musa', 'Ibrahim', 'Sani', 'Uche',
        'Obinna', 'Dele', 'Bode', 'Kola', 'Seun', 'Dapo', 'Nnamdi', 'Ifeanyi',
        'Chinedu', 'Babatunde', 'Abdullahi', 'Yusuf', 'Garba', 'Hassan', 'Aminu',
        'Oluwaseun', 'Chibuike', 'Damilola', 'Rotimi', 'Gbenga', 'Kayode', 'Biodun',
        'Abiodun', 'Olasubomi', 'Adekunle', 'Chukwudi', 'Ikenna', 'Tochukwu',
    ];

    private array $femaleFirstNames = [
        'Ngozi', 'Amaka', 'Funke', 'Adaeze', 'Chioma', 'Halima', 'Fatima', 'Aisha',
        'Yetunde', 'Shade', 'Bimpe', 'Kemi', 'Blessing', 'Grace', 'Zainab', 'Hauwa',
        'Rukayat', 'Folake', 'Adeola', 'Chinwe', 'Ifeoma', 'Uzoma', 'Temitope',
        'Oluwakemi', 'Adaobi', 'Rhoda', 'Mariam', 'Jumoke', 'Yewande', 'Chiamaka',
        'Nkechi', 'Ogechi', 'Precious', 'Sandra', 'Tokunbo', 'Vivian',
    ];

    private array $lastNames = [
        'Okafor', 'Adeyemi', 'Musa', 'Ibrahim', 'Okonkwo', 'Eze', 'Nwosu', 'Abubakar',
        'Yusuf', 'Garba', 'Adeleke', 'Bakare', 'Afolabi', 'Ogundimu', 'Taiwo', 'Olawale',
        'Nwachukwu', 'Obiora', 'Adebanjo', 'Salami', 'Bello', 'Usman', 'Mohammed',
        'Abdullahi', 'Suleiman', 'Olawuyi', 'Adegoke', 'Ogundipe', 'Nwofor', 'Danjuma',
        'Chukwuemeka', 'Akande', 'Ayoola', 'Olusanya', 'Adeniyi', 'Olawole', 'Jimoh',
        'Okeke', 'Onyekwere', 'Igwe', 'Obi', 'Okpara', 'Amadi', 'Ihejirika', 'Dimgba',
    ];

    private array $specializations = ['Residential', 'Commercial', 'Land', 'Industrial', 'Mixed'];

    private array $banks = [
        'Access Bank', 'GTBank', 'Zenith Bank', 'First Bank of Nigeria',
        'UBA', 'Fidelity Bank', 'Union Bank of Nigeria', 'Kuda Bank', 'Opay',
    ];

    private array $states = [
        'Lagos', 'Abuja', 'Rivers', 'Kano', 'Oyo', 'Kaduna', 'Edo', 'Plateau',
        'Borno', 'Enugu', 'Anambra', 'Imo', 'Delta', 'Cross River', 'Delta',
        'Kwara', 'Ogun', 'Akwa Ibom', 'Ondo', 'Sokoto',
    ];

    private array $lgas = [
        'Ikeja', 'Garki', 'Port Harcourt', 'Kano Municipal', 'Ibadan North',
        'Kaduna North', 'Benin City', 'Jos North', 'Maiduguri', 'Enugu North',
        'Onitsha North', 'Owerri Municipal', 'Warri South', 'Calabar Municipal',
        'Asaba', 'Ilorin West', 'Abeokuta South', 'Uyo', 'Akure South', 'Sokoto North',
    ];

    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('agency_partners')->truncate();
        // Remove only agency-linked realtors (those seeded by this seeder previously)
        DB::table('users_tables')->where('Email', 'like', 'agyrealtor%@realtorscircle.test')->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $passwordHash = Hash::make('Password@123');
        $loginSummary = [];

        foreach ($this->agencies as $index => $data) {
            $companySlug  = strtolower(preg_replace('/[^a-z0-9]/i', '', str_replace(' ', '', $data['company'])));
            $email        = "{$companySlug}@realtorscircle.test";

            $agency = AgencyPartner::create([
                'company_name'  => $data['company'],
                'first_name'    => $data['first'],
                'last_name'     => $data['last'],
                'email'         => $email,
                'phone'         => $data['phone'],
                'password'      => $passwordHash,
                'referral_code' => AgencyPartner::generateReferralCode(),
                'status'        => 'active',
            ]);

            $loginSummary[] = [
                'id'       => $agency->id,
                'company'  => $agency->company_name,
                'email'    => $email,
                'password' => 'Password@123',
                'ref_code' => $agency->referral_code,
            ];

            // Seed 50 realtors linked to this agency
            $this->seedRealtors($agency->id, $index, $passwordHash);
        }

        $this->command->newLine();
        $this->command->info('═══════════════════════════════════════════════════════════════');
        $this->command->info('  Agency Partner Logins (password: Password@123 for all)');
        $this->command->info('═══════════════════════════════════════════════════════════════');

        $rows = array_map(fn ($a) => [
            $a['id'],
            $a['company'],
            $a['email'],
            $a['ref_code'],
        ], $loginSummary);

        $this->command->table(['ID', 'Company', 'Login Email', 'Referral Code'], $rows);
        $this->command->newLine();
        $this->command->info('✓ 20 agencies seeded · 50 realtors each = 1,000 agency-linked realtors');
    }

    private function seedRealtors(int $agencyId, int $agencyIndex, string $passwordHash): void
    {
        $state = $this->states[$agencyIndex % count($this->states)];
        $lga   = $this->lgas[$agencyIndex % count($this->lgas)];

        for ($r = 0; $r < 50; $r++) {
            $isMale    = ($r % 2 === 0);
            $firstName = $isMale
                ? $this->maleFirstNames[($agencyIndex * 7 + $r) % count($this->maleFirstNames)]
                : $this->femaleFirstNames[($agencyIndex * 5 + $r) % count($this->femaleFirstNames)];
            $lastName  = $this->lastNames[($agencyIndex * 11 + $r * 3) % count($this->lastNames)];

            $slug  = strtolower(preg_replace('/[^a-z]/i', '', $firstName));
            $email = "agyrealtor.a{$agencyIndex}r{$r}.{$slug}@realtorscircle.test";
            $phone = '0811' . str_pad((string) ($agencyIndex * 100 + $r + 1000), 7, '0', STR_PAD_LEFT);

            $user = UsersTable::create([
                'First_name'        => $firstName,
                'Last_name'         => $lastName,
                'Email'             => $email,
                'Phone'             => $phone,
                'email_verified_at' => now(),
                'Password'          => $passwordHash,
                'Bio'               => "Experienced real estate professional with " . ($r % 15 + 2) . " years in {$state}.",
                'Address'           => "Plot " . ($r + 1) . ", {$lga} Estate, {$state}",
                'Specialization'    => $this->specializations[$r % count($this->specializations)],
                'Yrs_experience'    => (string) ($r % 18 + 2),
                'Notification'      => null,
                '2fa'               => '0',
                'Status'            => 'active',
                'State'             => $state,
                'Local_area'        => $lga,
                'Lga'               => $lga,
                'Country'           => 'Nigeria',
                'steps_signup'      => '5',
                'api_token'         => Str::random(60),
                'kyc_front_url'     => "https://picsum.photos/seed/kycf{$agencyIndex}{$r}/400/300",
                'kyc_back_url'      => "https://picsum.photos/seed/kycb{$agencyIndex}{$r}/400/300",
                'kyc_status'        => 'approved',
                'kyc_id_type'       => 'NIN Card',
                'agency_id'         => $agencyId,
            ]);

            BankInformation::create([
                'user_id'      => $user->id,
                'Account_Name' => "{$firstName} {$lastName}",
                'Bank_Name'    => $this->banks[($agencyIndex + $r) % count($this->banks)],
                'Bank_Account' => str_pad((string) ($agencyIndex * 1000 + $r + 2000000000), 10, '0', STR_PAD_LEFT),
                'status'       => ($r % 3 === 0) ? 'approved' : 'pending',
            ]);
        }
    }
}
