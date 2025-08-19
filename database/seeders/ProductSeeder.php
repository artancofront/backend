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
        $policyIds   = Policy::pluck('id')->toArray();

        $leafCategories = Category::doesntHave('children')->get();

        foreach ($leafCategories as $category) {
            for ($i = 1; $i <= 10; $i++) {
                $name = "محصول {$i} از دسته {$category->name}";
                $sku  = Str::slug($category->slug . '-' . $i . '-' . Str::random(4));

                // Pick random policies (0..all safe)
                $randomPolicyIds = [];
                if (!empty($policyIds)) {
                    $pick = array_rand(array_flip($policyIds), rand(1, count($policyIds)));
                    $randomPolicyIds = is_array($pick) ? $pick : [$pick];
                }

                // Pick random warranties (0..all safe)
                $randomWarrantyIds = [];
                if (!empty($warrantyIds)) {
                    $pick = array_rand(array_flip($warrantyIds), rand(1, count($warrantyIds)));
                    $randomWarrantyIds = is_array($pick) ? $pick : [$pick];
                }

                $product = Product::create([
                    'name'           => $name,
                    'slug'           => Str::slug($name) . '-' . $sku,
                    'description'    => "توضیحات مربوط به {$name} برای نمایش جزئیات محصول.",
                    'weight'         => rand(100, 2000) / 100,
                    'length'         => rand(10, 100) / 10,
                    'width'          => rand(10, 100) / 10,
                    'height'         => rand(10, 100) / 10,
                    'stock'          => rand(5, 100),
                    'sku'            => $sku,
                    'price'          => rand(100000, 10000000) / 100,
                    'has_variants'   => true,
                    'specifications' => null,
                    'expert_review'  => $this->generateExpertReview($name),
                    // FIX: correct fields
                    'warranties'     => $randomWarrantyIds,
                    'policies'       => $randomPolicyIds,
                    'category_id'    => $category->id,
                    'is_active'      => true,
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
            $value = CategoryAttributeValue::where('category_attribute_id', $attribute->id)
                ->inRandomOrder()
                ->first();

            if (!$value) {
                // No values for this attribute; skip
                continue;
            }

            ProductAttribute::create([
                'product_id'                 => $product->id,
                'category_attribute_id'      => $attribute->id,
                'category_attribute_value_id'=> $value->id,
            ]);
        }
    }

    private function createVariants($parentProduct, $category)
    {
        // Use unique attributes across the category chain
        $attributes = $this->getAllAttributes($category);

        // Parent's chosen values keyed by attribute
        $parentAttributes = $parentProduct->attributes->keyBy('category_attribute_id');

        for ($v = 1; $v <= 2; $v++) {
            $variantSku = $parentProduct->sku . '-v' . $v;

            $variant = Product::create([
                'parent_id' => $parentProduct->id,
                'sku'       => $variantSku,
                'price'     => $parentProduct->price + rand(10000, 50000) / 100,
                'stock'     => rand(1, 50),
                'is_active' => true,
            ]);

            // Pick a safe subset of attributes (1..2 but not exceeding available)
            $pickCount = min($attributes->count(), rand(1, 2));
            if ($pickCount === 0) {
                continue; // no attributes to vary
            }
            $subset = $attributes->random($pickCount);
            // Normalize to collection even if single item
            $subset = $subset instanceof \Illuminate\Support\Collection ? $subset : collect([$subset]);

            foreach ($subset as $attribute) {
                $allValues = CategoryAttributeValue::where('category_attribute_id', $attribute->id)->get();
                if ($allValues->isEmpty()) {
                    // No values for this attribute; skip
                    continue;
                }

                $parentValueId = optional($parentAttributes->get($attribute->id))->category_attribute_value_id;

                $filteredValues = $allValues->filter(fn ($val) => $val->id !== $parentValueId);

                $selectedValue = $filteredValues->isNotEmpty()
                    ? $filteredValues->random()
                    : $allValues->random();

                ProductAttribute::create([
                    'product_id'                 => $variant->id,
                    'category_attribute_id'      => $attribute->id,
                    'category_attribute_value_id'=> $selectedValue->id,
                ]);
            }
        }
    }

    private function getAllAttributes($category)
    {
        $attributes = collect();

        while ($category) {
            // assuming relation `$category->attributes`
            $attributes = $attributes->merge($category->attributes);
            $category = $category->parent;
        }

        // ensure uniqueness by id to avoid duplicates from parent/child overlap
        return $attributes->unique('id')->values();
    }

    private function generateExpertReview($productName)
    {
        $safeName = htmlspecialchars($productName, ENT_QUOTES, 'UTF-8');

        return [
            'html' => "
            <h2>بررسی تخصصی {$safeName}</h2>
            <p>این محصول دارای کیفیت بالا، عملکرد قابل اعتماد و طراحی منحصر به فردی است که نیازهای روزمره شما را به خوبی برآورده می‌کند.</p>
            <p>مزایای اصلی این محصول شامل موارد زیر می‌باشد:</p>
            <ul>
                <li>کیفیت ساخت عالی</li>
                <li>قیمت مناسب نسبت به عملکرد</li>
                <li>پشتیبانی از گارانتی معتبر</li>
            </ul>
            <img src='/images/review/{$safeName}.jpg' alt='بررسی تخصصی {$safeName}' />
            ",
        ];
    }
}
