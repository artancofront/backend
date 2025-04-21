<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\CategoryAttribute;
use App\Models\CategoryAttributeValue;
use App\Models\ProductAttribute;
use App\Models\Warranty;
use App\Models\Policy;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $warrantyIds = Warranty::pluck('id')->toArray();
        $policyIds = Policy::pluck('id')->toArray();

        $leafCategories = Category::doesntHave('children')->get();

        foreach ($leafCategories as $category) {
            for ($i = 1; $i <= 10; $i++) {
                $name = "محصول {$i} از دسته {$category->name}";
                $sku = Str::slug($category->slug . '-' . $i . '-' . Str::random(4));

                $random = array_rand(array_flip($policyIds), rand(1, count($policyIds)));
                $randomPolicyIds = is_array($random) ? $random : [$random];
                $random = array_rand(array_flip($warrantyIds), rand(1, count($warrantyIds)));
                $randomWarrantyIds = is_array($random) ? $random : [$random];

                $product = Product::create([
                    'name' => $name,
                    'slug' => Str::slug($name) . '-' . $sku,
                    'description' => "توضیحات مربوط به {$name} برای نمایش جزئیات محصول.",
                    'weight' => rand(100, 2000) / 100,
                    'length' => rand(10, 100) / 10,
                    'width' => rand(10, 100) / 10,
                    'height' => rand(10, 100) / 10,
                    'stock' => rand(5, 100),
                    'sku' => $sku,
                    'price' => rand(100000, 10000000) / 100,
                    'has_variants' => true,
                    'specifications' => null,
                    'expert_review' => $this->generateExpertReview($name),
                    'warranties' => $randomPolicyIds,
                    'policies' => $randomWarrantyIds,
                    'category_id' => $category->id,
                    'is_active' => true,
                ]);

                // Assign attributes to parent product
                $this->assignAttributes($product, $category);

                // Create variants
                $this->createVariants($product, $category);
            }
        }
    }

    private function assignAttributes($product, $category)
    {
        $attributes = $this->getAllAttributes($category);

        foreach ($attributes as $attribute) {
            $value = CategoryAttributeValue::where('category_attribute_id', $attribute->id)->inRandomOrder()->first();
            if ($value) {
                ProductAttribute::create([
                    'product_id' => $product->id,
                    'category_attribute_id' => $attribute->id,
                    'category_attribute_value_id' => $value->id,
                ]);
            }
        }
    }

    private function createVariants($parentProduct, $category)
    {
        $attributes = $this->getAllAttributes($category);

        // Get parent's attributes and their selected value IDs
        $parentAttributes = $parentProduct->attributes->keyBy('category_attribute_id');

        for ($v = 1; $v <= 2; $v++) {
            $variantSku = $parentProduct->sku . '-v' . $v;
            $variant = Product::create([
                'parent_id' => $parentProduct->id,
                'sku' => $variantSku,
                'price' => $parentProduct->price + rand(10000, 50000) / 100,
                'stock' => rand(1, 50),
                'is_active' => true,
            ]);

            // Assign a subset of attributes with different values
            $subset = $attributes->random(rand(1,2));

            foreach ($subset as $attribute) {
                $allValues = CategoryAttributeValue::where('category_attribute_id', $attribute->id)->get();

                $parentValueId = $parentAttributes[$attribute->id]->category_attribute_value_id ?? null;

                $filteredValues = $allValues->filter(fn($val) => $val->id !== $parentValueId);

                // If we can't find a different value, fallback to any
                $selectedValue = $filteredValues->isNotEmpty()
                    ? $filteredValues->random()
                    : $allValues->random();

                ProductAttribute::create([
                    'product_id' => $variant->id,
                    'category_attribute_id' => $attribute->id,
                    'category_attribute_value_id' => $selectedValue->id,
                ]);
            }
        }
    }


    private function getAllAttributes($category)
    {
        $attributes = collect();

        while ($category) {
            $attributes = $attributes->merge($category->attributes);
            $category = $category->parent;
        }

        return $attributes;
    }

    private function generateExpertReview($productName)
    {
        $safeName = htmlspecialchars($productName);
        return ['html'=> "
            <h2>بررسی تخصصی {$safeName}</h2>
            <p>این محصول دارای کیفیت بالا، عملکرد قابل اعتماد و طراحی منحصر به فردی است که نیازهای روزمره شما را به خوبی برآورده می‌کند.</p>
            <p>مزایای اصلی این محصول شامل موارد زیر می‌باشد:</p>
            <ul>
                <li>کیفیت ساخت عالی</li>
                <li>قیمت مناسب نسبت به عملکرد</li>
                <li>پشتیبانی از گارانتی معتبر</li>
            </ul>
            <img src='/images/review/{$safeName}.jpg' alt='بررسی تخصصی {$safeName}' />
            "];
    }
}

