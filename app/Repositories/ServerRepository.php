<?php

namespace App\Repositories;

use App\Models\Server;

class ServerRepository
{
    /**
     * Get all servers.
     *
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all(array $columns = ['*'])
    {
        return Server::all($columns);
    }

    /**
     * Find server by ID.
     *
     * @param int $id
     * @param array $columns
     * @return Server|null
     */
    public function find(int $id, array $columns = ['*']): ?Server
    {
        return Server::find($id, $columns);
    }

    /**
     * Create a new server.
     *
     * @param array $data
     * @return Server
     */
    public function create(array $data): Server
    {
        return Server::create($data);
    }

    /**
     * Update an existing server.
     *
     * @param int $id
     * @param array $data
     * @return Server|null
     */
    public function update(int $id, array $data): ?Server
    {
        $server = $this->find($id);

        if (!$server) {
            return null;
        }

        $server->update($data);

        return $server;
    }

    /**
     * Delete a server.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $server = $this->find($id);

        if (!$server) {
            return false;
        }

        return (bool) $server->delete();
    }
}
