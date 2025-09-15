<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Warranty;

class WarrantySeeder extends Seeder
{
    public function run()
    {
        Warranty::insert([
            ['name' => 'گارانتی ۱۲ ماهه', 'cost' => 50000],
            ['name' => 'گارانتی ۲۴ ماهه', 'cost' => 90000],
            ['name' => 'گارانتی تعویض ۷ روزه', 'cost' => 0],
        ]);
    }
}

