<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $roles= [
            [
                'name' => 'مدیر کل',
                'description' => 'دسترسی کامل به همه بخش‌ها و عملیات سیستم',
                'permissions' => ['all' => ['all']],
            ],
            [
                'name' => 'مدیر سیستم',
                'description' => 'دسترسی به تمام بخش‌ها به جز کاربران و نقش‌ها',
                'permissions' => [
                    'products' => ['all'],
                    'categories' => ['all'],
                    'orders' => ['all'],
                    'blogs' => ['all'],
                    'settings' => ['all'],
                    'seo' => ['all'],
                ],
            ],
            [
                'name' => 'مدیر محتوا',
                'description' => 'مدیریت محتوا، محصولات و وبلاگ‌ها، بدون دسترسی به تنظیمات',
                'permissions' => [
                    'products' => ['all'],
                    'categories' => ['all'],
                    'blogs' => ['all'],
                    'seo' => ['all'],
                ],
            ],
            [
                'name' => 'مدیر فروش',
                'description' => 'مدیریت سفارش‌ها، سبد خرید، علاقه‌مندی‌ها و آدرس‌ها',
                'permissions' => [
                    'orders' => ['all'],
                    'carts' => ['all'],
                    'wishlists' => ['all'],
                    'addresses' => ['all'],
                ],
            ],
            [
                'name' => 'ویراستار',
                'description' => 'ویرایش و مدیریت وبلاگ و سئو',
                'permissions' => [
                    'blogs' => ['all'],
                    'seo' => ['all'],
                ],
            ],
        ];


        foreach ($roles as $role) {
            Role::create([
                'name' => $role['name'],
                'description' => $role['description'],
                'permissions' => $role['permissions'],
            ]);
        }
    }
}
