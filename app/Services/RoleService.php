<?php

namespace App\Services;

use App\Repositories\RoleRepository;
use App\Models\Role;

class RoleService
{
    protected RoleRepository $roleRepository;

    public function __construct(RoleRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    public function getAllRoles()
    {
        return $this->roleRepository->all();
    }

    public function getRoleById(int $id): ?Role
    {
        return $this->roleRepository->find($id);
    }

    public function createRole(array $data): Role
    {
        return $this->roleRepository->create($data);
    }

    public function updateRole(int $id, array $data): Role
    {
        return $this->roleRepository->update($id, $data);
    }

    public function deleteRole(int $id): bool
    {
        return $this->roleRepository->delete($id);
    }

    public function setRolePermission(int $roleId, string $category, array $actions): void
    {
        $this->roleRepository->setPermission($roleId, $category, $actions);
    }

    public function getPermissions(int $roleId): array
    {
        return $this->roleRepository->getPermissions($roleId);
    }
}
