<?php

namespace App\Repositories;

use App\Models\CmsDeployment;

class CmsDeploymentRepository
{
    /**
     * Get all CMS deployments.
     *
     * @param array $with
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all(array $with = [])
    {
        return CmsDeployment::with($with)->get();
    }

    /**
     * Find a CMS deployment by ID.
     *
     * @param int $id
     * @param array $with
     * @return CmsDeployment|null
     */
    public function find(int $id, array $with = [])
    {
        return CmsDeployment::with($with)->find($id);
    }

    /**
     * Create a new CMS deployment.
     *
     * @param array $data
     * @return CmsDeployment
     */
    public function create(array $data): CmsDeployment
    {
        return CmsDeployment::create($data);
    }

    /**
     * Update an existing CMS deployment.
     *
     * @param int $id
     * @param array $data
     * @return CmsDeployment|null
     */
    public function update(int $id, array $data): ?CmsDeployment
    {
        $deployment = $this->find($id);

        if (! $deployment) {
            return null;
        }

        $deployment->update($data);
        return $deployment;
    }

    /**
     * Delete a CMS deployment.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $deployment = $this->find($id);

        if (! $deployment) {
            return false;
        }

        return (bool) $deployment->delete();
    }
}
