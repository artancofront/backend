<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Policy;

class PolicySeeder extends Seeder
{
    public function run()
    {
        Policy::insert([
            ['type' => 'shipment', 'label' => 'ارسال رایگان', 'icon' => 'free-shipping.png'],
            ['type' => 'refund', 'label' => 'بازگشت ۷ روزه', 'icon' => 'refund.png'],
            ['type' => 'payment', 'label' => 'پرداخت در محل', 'icon' => 'cod.png'],
        ]);
    }
}
