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
    case AuditUser = 'audit_user';
    case ViewRole = 'view_role';
    case AddRole = 'add_role';
    case EditRole = 'edit_role';
    case DeleteRole = 'delete_role';
    case ApproveRole = 'approve_role';
    case AuditRole = 'audit_role';
    case ViewWhatsApp = 'view_whatsapp';
    case AddWhatsApp = 'add_whatsapp';
    case EditWhatsApp = 'edit_whatsapp';
    case DeleteWhatsApp = 'delete_whatsapp';
    case ApproveWhatsApp = 'approve_whatsapp';
    case AuditWhatsApp = 'audit_whatsapp';
    case ViewApprovalFlow = 'view_approval_flow';
    case AddApprovalFlow = 'add_approval_flow';
    case EditApprovalFlow = 'edit_approval_flow';
    case DeleteApprovalFlow = 'delete_approval_flow';
    case ApproveApprovalFlow = 'approve_approval_flow';
    case AuditApprovalFlow = 'audit_approval_flow';

    public static function values(): array
    {
        return array_map(fn (self $enum): string => $enum->value, self::cases());
    }

    public static function module(): array
    {
        return [
            'user', 'role', 'whatsapp', 'approval_flow'
        ];
    }

    public static function action(): array
    {
        return [
            'view', 'add', 'edit', 'delete', 'approve', 'audit',
        ];
    }

    public static function skip(): array
    {
        return [
            'approve_user', 'approve_role',
            'approve_whatsapp', 'audit_whatsapp',
            'approve_approval_flow', 'audit_approval_flow',
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
