<?php

namespace Database\Seeders;

use App\Data\NigeriaLocations;
use App\Models\AgentsTable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AgentSeeder extends Seeder
{
    private array $specializations = ['Residential', 'Commercial', 'Land', 'Industrial', 'Mixed'];

    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('agents_tables')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $passwordHash = Hash::make('Password@123');
        $states       = NigeriaLocations::statesWithLgas();
        $stateIndex   = 0;
        $total        = 0;

        foreach ($states as $state => $lgas) {
            foreach ($lgas as $lgaIndex => $lga) {
                // Strip spaces and special chars for a clean last name slug
                $lastNameSlug = preg_replace('/[^a-zA-Z0-9]/', '', str_replace(' ', '', $lga));

                // Email: no dots before @
                $email = "agent{$stateIndex}x{$lgaIndex}@realtorscircle.test";

                // Phone: deterministic 11-digit number
                $phoneNum = 8000000000 + ($stateIndex * 100) + $lgaIndex;

                AgentsTable::create([
                    'First_name'     => 'Agent',
                    'Last_name'      => $lastNameSlug,
                    'Email'          => $email,
                    'Password'       => $passwordHash,
                    'Phone'          => (string) $phoneNum,
                    'Assigned_area'  => $lga,
                    'State'          => $state,
                    'Specialization' => $this->specializations[($stateIndex + $lgaIndex) % count($this->specializations)],
                    'Status'         => 'active',
                    'api_token'      => Str::random(60),
                ]);

                $total++;
            }

            $stateIndex++;
        }

        $this->command->info("✓ {$total} agents seeded across {$stateIndex} states (1 per LGA).");
    }
}
