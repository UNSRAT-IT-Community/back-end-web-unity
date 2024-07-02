<?php

namespace App\Http\Repositories;

use App\Http\Interfaces\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class UserRepository implements UserRepositoryInterface
{
    /**
     * Get all users from the database with optional specific columns.
     *
     * @param array $columns
     * @return Collection
     */
    public function getAllUsers(array $columns = ['*']): Collection
    {
        return User::select($columns)->get();
    }

    /**
     * Get a single user by ID with specific columns.
     *
     * @param int $id
     * @param array $columns
     * @return Model|null
     */
    public function getUserById(string $id, array $columns = ['id', 'name', 'nim', 'email', 'profile_picture', 'role_id', 'division_id', 'is_accepted']): ?Model
    {
        return User::select($columns)->where('id', $id)->first();
    }
}