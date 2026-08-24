<?php

namespace Database\Seeders;

use App\Data\NigeriaLocations;
use App\Models\BankInformation;
use App\Models\UsersTable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    private array $maleNames = [
        'Emeka', 'Tunde', 'Chidi', 'Femi', 'Musa', 'Ibrahim', 'Sani', 'Uche',
        'Obinna', 'Dele', 'Bode', 'Kola', 'Seun', 'Dapo', 'Nnamdi', 'Ifeanyi',
        'Chinedu', 'Babatunde', 'Abdullahi', 'Yusuf', 'Garba', 'Hassan', 'Aminu',
        'Oluwaseun', 'Chibuike', 'Damilola', 'Rotimi', 'Gbenga', 'Kayode', 'Biodun',
    ];

    private array $femaleNames = [
        'Ngozi', 'Amaka', 'Funke', 'Adaeze', 'Chioma', 'Halima', 'Fatima', 'Aisha',
        'Yetunde', 'Shade', 'Bimpe', 'Kemi', 'Blessing', 'Grace', 'Zainab', 'Hauwa',
        'Rukayat', 'Folake', 'Adeola', 'Chinwe', 'Ifeoma', 'Uzoma', 'Temitope',
        'Oluwakemi', 'Adaobi', 'Rhoda', 'Mariam', 'Jumoke', 'Yewande', 'Chiamaka',
    ];

    private array $lastNames = [
        'Okafor', 'Adeyemi', 'Musa', 'Ibrahim', 'Okonkwo', 'Eze', 'Nwosu', 'Abubakar',
        'Yusuf', 'Garba', 'Adeleke', 'Bakare', 'Afolabi', 'Ogundimu', 'Taiwo', 'Olawale',
        'Nwachukwu', 'Obiora', 'Adebanjo', 'Salami', 'Bello', 'Usman', 'Mohammed',
        'Abdullahi', 'Suleiman', 'Olawuyi', 'Adegoke', 'Ogundipe', 'Nwofor', 'Danjuma',
        'Chukwuemeka', 'Akande', 'Ayoola', 'Olusanya', 'Adeniyi', 'Olawole', 'Jimoh',
    ];

    private array $specializations = [
        'Residential', 'Commercial', 'Land', 'Industrial', 'Mixed',
    ];

    private array $banks = [
        'Access Bank', 'GTBank', 'Zenith Bank', 'First Bank of Nigeria',
        'UBA', 'Fidelity Bank', 'Union Bank of Nigeria', 'Sterling Bank',
        'Kuda Bank', 'Opay', 'Wema Bank', 'Polaris Bank',
    ];

    private array $idTypes = [
        'NIN Card', 'International Passport', "Driver's License", "Voter's Card",
    ];

    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('bank_information')->truncate();
        DB::table('users_tables')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $passwordHash = Hash::make('Password@123');
        $states       = NigeriaLocations::statesWithLgas();
        $phoneBase    = 8001000001;
        $stateIndex   = 0;

        foreach ($states as $state => $lgas) {
            for ($u = 0; $u < 3; $u++) {
                $isMale    = ($u % 2 === 0);
                $firstName = $isMale
                    ? $this->maleNames[($stateIndex * 3 + $u) % count($this->maleNames)]
                    : $this->femaleNames[($stateIndex + $u) % count($this->femaleNames)];
                $lastName  = $this->lastNames[($stateIndex * 7 + $u * 3) % count($this->lastNames)];
                $lga       = $lgas[($stateIndex + $u) % count($lgas)];

                $slug  = strtolower(preg_replace('/[^a-z0-9]/i', '', $firstName));
                $email = "{$slug}.s{$stateIndex}u{$u}@realtorscircle.test";
                $phone = (string) ($phoneBase + $stateIndex * 3 + $u);

                $bio = "Dedicated real estate professional based in {$lga}, {$state} with "
                     . rand(2, 18) . " years of experience. I specialise in helping clients "
                     . "find the perfect property across Nigeria.";

                $user = UsersTable::create([
                    'First_name'       => $firstName,
                    'Last_name'        => $lastName,
                    'Email'            => $email,
                    'Phone'            => $phone,
                    'email_verified_at' => now(),
                    'Password'         => $passwordHash,
                    'Bio'              => $bio,
                    'Address'          => "Plot " . rand(1, 50) . ", {$lga} Estate, {$state}",
                    'Specialization'   => $this->specializations[$u % count($this->specializations)],
                    'Yrs_experience'   => (string) rand(2, 20),
                    'Notification'     => null,
                    '2fa'              => '0',
                    'Status'           => 'active',
                    'State'            => $state,
                    'Local_area'       => $lga,
                    'Lga'              => $lga,
                    'Country'          => 'Nigeria',
                    'steps_signup'     => '5',
                    'api_token'        => Str::random(60),
                    'kyc_front_url'    => "https://picsum.photos/seed/kycf{$stateIndex}{$u}/400/300",
                    'kyc_back_url'     => "https://picsum.photos/seed/kycb{$stateIndex}{$u}/400/300",
                    'kyc_status'       => 'approved',
                    'kyc_id_type'      => $this->idTypes[($stateIndex + $u) % count($this->idTypes)],
                ]);

                BankInformation::create([
                    'user_id'      => $user->id,
                    'Account_Name' => "{$firstName} {$lastName}",
                    'Bank_Name'    => $this->banks[($stateIndex + $u) % count($this->banks)],
                    'Bank_Account' => str_pad((string) ($stateIndex * 3 + $u + 1000000000), 10, '0', STR_PAD_LEFT),
                    'status'       => 'pending',
                ]);
            }

            $stateIndex++;
        }

        $total = $stateIndex * 3;
        $this->command->info("✓ {$total} users seeded across {$stateIndex} states.");
    }
}
