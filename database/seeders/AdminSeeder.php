<?php

namespace Database\Seeders;

use App\Models\AdminsTable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('admins_tables')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        AdminsTable::create([
            'First_name' => 'Super',
            'Last_name'  => 'Admin',
            'Email'      => 'admin@realtorscircle.test',
            'Password'   => Hash::make('Admin@1234'),
            'Phone'      => '08000000000',
            'Role'       => 'super_admin',
            'Status'     => 'active',
            'api_token'  => Str::random(80),
        ]);

        $this->command->info('✓ Admin seeded — admin@realtorscircle.test / Admin@1234');
    }
}
