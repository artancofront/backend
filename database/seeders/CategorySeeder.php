<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\CategoryAttribute;
use App\Models\CategoryAttributeValue;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            ['name' => 'لوازم خانگی', 'slug' => 'home-appliances'],
            ['name' => 'مد و پوشاک', 'slug' => 'fashion'],
            ['name' => 'سلامت و زیبایی', 'slug' => 'health-beauty'],
            ['name' => 'الکترونیک', 'slug' => 'electronics'],
            ['name' => 'کودکان', 'slug' => 'children'],
            ['name' => 'کتاب و لوازم تحریر', 'slug' => 'books-stationery'],
            ['name' => 'خواربار', 'slug' => 'groceries'],
        ];

        foreach ($categories as $index => $categoryData) {
            $category = Category::create($categoryData);

            // Parent-level attribute (e.g., ارسال سریع)
            $fastDeliveryAttr = CategoryAttribute::create([
                'category_id' => $category->id,
                'name' => 'ارسال سریع',
            ]);

            CategoryAttributeValue::create([
                'category_attribute_id' => $fastDeliveryAttr->id,
                'value' => 'دارد',
            ]);

            CategoryAttributeValue::create([
                'category_attribute_id' => $fastDeliveryAttr->id,
                'value' => 'ندارد',
            ]);

            // Subcategories and their specific attributes
            if ($index === 0) { // لوازم خانگی
                $sub1 = $category->children()->create(['name' => 'تجهیزات خانگی', 'slug' => 'home-equipment']);
                $sub2 = $category->children()->create(['name' => 'لوازم آشپزخانه', 'slug' => 'kitchen-appliances']);

                foreach ([$sub1, $sub2] as $sub) {
                    $brand = CategoryAttribute::create(['category_id' => $sub->id, 'name' => 'برند']);
                    $color = CategoryAttribute::create(['category_id' => $sub->id, 'name' => 'رنگ']);
                    $model = CategoryAttribute::create(['category_id' => $sub->id, 'name' => 'مدل']);

                    foreach (['سامسونگ', 'ال‌جی', 'دوو'] as $val) {
                        CategoryAttributeValue::create(['category_attribute_id' => $brand->id, 'value' => $val]);
                    }
                    foreach (['سفید', 'نقره‌ای', 'مشکی'] as $val) {
                        CategoryAttributeValue::create(['category_attribute_id' => $color->id, 'value' => $val]);
                    }
                    foreach (['2023', '2024', 'اکو'] as $val) {
                        CategoryAttributeValue::create(['category_attribute_id' => $model->id, 'value' => $val]);
                    }
                }
            }

            if ($index === 1) { // مد و پوشاک
                $sub1 = $category->children()->create(['name' => 'مردانه', 'slug' => 'men']);
                $sub2 = $category->children()->create(['name' => 'زنانه', 'slug' => 'women']);

                foreach ([$sub1, $sub2] as $sub) {
                    $size = CategoryAttribute::create(['category_id' => $sub->id, 'name' => 'سایز']);
                    $material = CategoryAttribute::create(['category_id' => $sub->id, 'name' => 'جنس']);
                    $pattern = CategoryAttribute::create(['category_id' => $sub->id, 'name' => 'طرح']);

                    foreach (['S', 'M', 'L', 'XL'] as $val) {
                        CategoryAttributeValue::create(['category_attribute_id' => $size->id, 'value' => $val]);
                    }
                    foreach (['کتان', 'پلی‌استر', 'چرم'] as $val) {
                        CategoryAttributeValue::create(['category_attribute_id' => $material->id, 'value' => $val]);
                    }
                    foreach (['ساده', 'گلدار', 'راه‌راه'] as $val) {
                        CategoryAttributeValue::create(['category_attribute_id' => $pattern->id, 'value' => $val]);
                    }
                }
            }

            if ($index === 3) { // الکترونیک
                $sub1 = $category->children()->create(['name' => 'گوشی موبایل', 'slug' => 'mobile-phones']);
                $sub2 = $category->children()->create(['name' => 'کامپیوتر و لپ تاپ', 'slug' => 'computers-laptops']);

                foreach ([$sub1, $sub2] as $sub) {
                    $storage = CategoryAttribute::create(['category_id' => $sub->id, 'name' => 'ظرفیت حافظه']);
                    $screen = CategoryAttribute::create(['category_id' => $sub->id, 'name' => 'سایز صفحه‌نمایش']);
                    $brand = CategoryAttribute::create(['category_id' => $sub->id, 'name' => 'برند']);

                    foreach (['64GB', '128GB', '256GB'] as $val) {
                        CategoryAttributeValue::create(['category_attribute_id' => $storage->id, 'value' => $val]);
                    }
                    foreach (['5.5"', '6.1"', '6.7"'] as $val) {
                        CategoryAttributeValue::create(['category_attribute_id' => $screen->id, 'value' => $val]);
                    }
                    foreach (['اپل', 'سامسونگ', 'شیائومی'] as $val) {
                        CategoryAttributeValue::create(['category_attribute_id' => $brand->id, 'value' => $val]);
                    }
                }
            }
        }
    }
}
