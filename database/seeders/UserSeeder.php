<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'Bartels',
            'firstname' => 'Jeroen',
            'email' => 'jeroen@okappi.be',
            'email_verified_at' => now(),
            'password' => bcrypt('@dmin123'),
            'password_plain' => '@dmin123',
            'role' => 'Admin',
            'supplier_code' => '123456789',
        ]);
    }
}
