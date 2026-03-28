<?php

namespace App\Modules\User\Services;

use App\Modules\User\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function getAll(int $perPage = 15): LengthAwarePaginator
    {
        return User::with('roles')->paginate($perPage);
    }

    public function getById(int $id): ?User
    {
        return User::with('roles')->find($id);
    }

    public function update(int $id, array $data): User
    {
        $user = User::findOrFail($id);

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);

        if (isset($data['role'])) {
            $user->syncRoles([$data['role']]);
        }

        return $user->fresh(['roles']);
    }

    public function delete(int $id): bool
    {
        return (bool) User::findOrFail($id)->delete();
    }
}
