<?php

namespace App\Repositories;

use App\Models\Warranty;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Exception;

class WarrantyRepository
{
    /**
     * Get all warranties.
     *
     * @return Collection
     */
    public function all(): Collection
    {
        return Warranty::all();
    }

    /**
     * Find a warranty by its ID.
     *
     * @param int $id
     * @return Warranty
     * @throws Exception
     */
    public function find(int $id): Warranty
    {
        $warranty = Warranty::find($id);

        if (!$warranty) {
            throw new Exception("Warranty not found.");
        }

        return $warranty;
    }

    /**
     * Create a new warranty.
     *
     * @param array $data
     * @return Warranty
     */
    public function create(array $data): Warranty
    {
        return Warranty::create($data);
    }

    /**
     * Update an existing warranty.
     *
     * @param int $id
     * @param array $data
     * @return Warranty
     * @throws Exception
     */
    public function update(int $id, array $data): Warranty
    {
        $warranty = $this->find($id);
        $warranty->update($data);
        return $warranty;
    }

    /**
     * Delete a warranty by its ID.
     *
     * @param int $id
     * @return bool
     * @throws Exception
     */
    public function delete(int $id): bool
    {
        $warranty = $this->find($id);
        return $warranty->delete();
    }
}
