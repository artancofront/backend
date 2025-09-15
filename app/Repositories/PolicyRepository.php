<?php

namespace App\Repositories;

use App\Models\Policy;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Exception;

class PolicyRepository
{
    /**
     * Get all policies.
     *
     * @return Collection
     */
    public function all(): Collection
    {
        return Policy::all();
    }

    /**
     * Find a policy by its ID.
     *
     * @param int $id
     * @return Policy
     * @throws Exception
     */
    public function find(int $id): Policy
    {
        $policy = Policy::find($id);

        if (!$policy) {
            throw new Exception("Policy not found.");
        }

        return $policy;
    }

    /**
     * Create a new policy.
     *
     * @param array $data
     * @return Policy
     */
    public function create(array $data): Policy
    {
        return Policy::create($data);
    }

    /**
     * Update an existing policy.
     *
     * @param int $id
     * @param array $data
     * @return Policy
     * @throws Exception
     */
    public function update(int $id, array $data): Policy
    {
        $policy = $this->find($id);
        $policy->update($data);
        return $policy;
    }

    /**
     * Delete a policy by its ID.
     *
     * @param int $id
     * @return bool
     * @throws Exception
     */
    public function delete(int $id): bool
    {
        $policy = $this->find($id);
        return $policy->delete();
    }



}
