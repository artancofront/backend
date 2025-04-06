<?php


namespace App\Repositories;

use App\Models\CategoryAttribute;
use App\Models\CategoryAttributeValue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class CategoryAttributeRepository
{
    /**
     * Get all category attributes.
     */
    public function getAll(): Collection
    {
        return CategoryAttribute::all();
    }

    /**
     * Get a category attribute by its ID.
     */
    public function getById(int $id): ?CategoryAttribute
    {
        return CategoryAttribute::find($id);
    }

    /**
     * Get category attributes by category ID.
     */
    public function getByCategoryId(int $categoryId): Collection
    {
        return CategoryAttribute::where('category_id', $categoryId)->get();
    }

    /**
     * Create a new category attribute.
     */
    public function create(array $data): CategoryAttribute
    {
        return CategoryAttribute::create($data);
    }

    /**
     * Update an existing category attribute.
     */
    public function update(int $id, array $data): bool
    {
        $categoryAttribute = CategoryAttribute::find($id);

        if ($categoryAttribute) {
            return $categoryAttribute->update($data);
        }

        return false;
    }

    /**
     * Delete a category attribute.
     */
    public function delete(int $id): bool
    {
        $categoryAttribute = CategoryAttribute::find($id);

        if ($categoryAttribute) {
            return $categoryAttribute->delete();
        }

        return false;
    }


    /**
     * Get all values for a given category attribute.
     */
    public function getValuesForAttribute(int $attributeId): Collection
    {
        return CategoryAttributeValue::where('category_attribute_id', $attributeId)->get();
    }

    /**
     * Create a new value for a category attribute.
     */
    public function createValue(array $data): CategoryAttributeValue
    {
        return CategoryAttributeValue::create($data);
    }

    /**
     * Update an existing category attribute value.
     */
    public function updateValue(int $id, array $data): bool
    {
        $categoryAttributeValue = CategoryAttributeValue::find($id);

        if ($categoryAttributeValue) {
            return $categoryAttributeValue->update($data);
        }

        return false;
    }

    /**
     * Delete a category attribute value.
     */
    public function deleteValue(int $id): bool
    {
        $categoryAttributeValue = CategoryAttributeValue::find($id);

        if ($categoryAttributeValue) {
            return $categoryAttributeValue->delete();
        }

        return false;
    }

}
