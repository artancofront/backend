<?php

namespace App\Repositories;

use App\Models\Role;
use Illuminate\Support\Collection;

class RoleRepository
{
    public function all(): Collection
    {
        return Role::all();
    }

    public function find(int $id): ?Role
    {
        return Role::find($id);
    }

    public function create(array $data): Role
    {
        return Role::create($data);
    }

    public function update(int $id, array $data): Role
    {
        $role = $this->find($id);
        if (!$role) {
            throw new \Exception("Role not found.");
        }

        $role->update($data);
        return $role;
    }

    public function delete(int $id): bool
    {
        $role = $this->find($id);
        if (!$role) {
            return false;
        }

        return $role->delete();
    }

    public function setPermission(int $roleId, string $category, array $actions): void
    {
        $role = $this->find($roleId);
        if (!$role) {
            throw new \Exception("Role not found.");
        }

        $role->setPermission($category, $actions);
    }

    public function hasPermission(int $roleId, string $category, string $action): bool
    {
        $role = $this->find($roleId);
        if (!$role) {
            return false;
        }

        return $role->hasPermission($category, $action);
    }

    public function getPermissions(int $roleId): array
    {
        $role = $this->find($roleId);
        return $role ? $role->getPermissions() : [];
    }
}
