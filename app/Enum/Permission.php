<?php

namespace App\Enum;

use Illuminate\Support\Facades\Gate;

enum Permission: string
{
    case ViewUser = 'view_user';
    case AddUser = 'add_user';
    case EditUser = 'edit_user';
    case DeleteUser = 'delete_user';
    case ApproveUser = 'approve_user';
    case ViewRole = 'view_role';
    case AddRole = 'add_role';
    case EditRole = 'edit_role';
    case DeleteRole = 'delete_role';
    case ApproveRole = 'approve_role';

    public static function values(): array
    {
        return array_map(fn (self $enum): string => $enum->value, self::cases());
    }

    public static function module(): array
    {
        return [
            'user', 'role',
        ];
    }

    public static function action(): array
    {
        return [
            'view', 'add', 'edit', 'delete', 'approve',
        ];
    }

    public static function skip(): array
    {
        return [
            'approve_user', 'approve_role',
        ];
    }

    public static function allows(): array
    {
        $allows = [];

        foreach (self::values() as $enum) {
            $allows[$enum] = Gate::allows($enum);
        }

        return $allows;
    }

    public function isAllowed(): bool
    {
        return Gate::allows($this->value);
    }

    public function asMiddleware(): string
    {
        return "can:" . $this->value;
    }
}
