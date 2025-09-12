<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Treasury;

class TreasurySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Treasury::firstOrCreate(
            ['name' => 'الخزينة الرئيسية'],
            ['balance' => 0]
        );
    }
}