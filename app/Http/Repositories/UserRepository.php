<?php

namespace App\Http\Repositories;

use App\Http\Interfaces\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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

    public function getUserRoleById(string $userId): ?Model
    {
        return User::select('role_id')->where('id', $userId)->first();
    }

    /**
     * Get role name by role ID, only returns "member", "coordinator", "committee" or null.
     *
     * @param string $roleId
     * @return string|null
     */
    public function getRoleNameById(string $roleId): ?string
    {
        $role = User::where('role_id', $roleId)->first()->role->name ?? null;
        $allowedRoles = ['member', 'coordinator', 'committee'];

        return in_array($role, $allowedRoles) ? $role : null;
    }

    public function getUserDataByRole()
    {
        return DB::table('users')
            ->join('roles', 'users.role_id', '=', 'roles.id')
            ->join('divisions', 'users.division_id', '=', 'divisions.id')
            ->select(
                'users.name',
                'users.nim',
                'users.email',
                'roles.name as role',
                'divisions.name as division',
                'users.is_accepted'
            )
            ->where('roles.name', 'Anggota')
            ->get();
    }
}