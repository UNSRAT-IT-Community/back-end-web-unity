<?php

namespace App\Http\Interfaces;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

interface UserRepositoryInterface
{
    /**
     * Get all users from the database with optional specific columns.
     *
     * @param array $columns
     * @return Collection
     */
    public function getAllUsers(array $columns = ['*']): Collection;

    /**
     * Get a single user by ID with specific columns.
     *
     * @param int $id
     * @param array $columns
     * @return Model|null
     */
    public function getUserById(string $id, array $columns = ['id', 'name', 'nim', 'email', 'profile_picture', 'role_id', 'division_id', 'is_accepted']): ?Model;
}