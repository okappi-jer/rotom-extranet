<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Administration;

class AdministrationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Administration::create([
            'name' => 'ROTOM',
            'token' => '156a070c-c2b3-4c7b-ac1e-511cf940c839',
        ]);

        Administration::create([
            'name' => 'DEPLECKER',
            'token' => '97508e4f-eb6a-4bb3-931f-62190a340993',
        ]);

        Administration::create([
            'name' => 'ORCA',
            'token' => 'ad5ae447-3957-42cc-9fd9-64ef57700fd4',
        ]);

        Administration::create([
            'name' => 'VERBOOMEN',
            'token' => '0584ea30-5385-4d2f-ba8f-1960b916a944',
        ]);
    }
}
