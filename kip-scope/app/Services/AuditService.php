<?php

namespace App\Services;

use App\Models\LogAudit;
use Illuminate\Support\Facades\Auth;

class AuditService
{
    public static function log(string $action, array $detail = []): void
    {
        try {
            LogAudit::create([
                'user_id' => Auth::id(),
                'aksi' => $action,
                'detail' => $detail,
            ]);
        } catch (\Throwable $e) {
            // Log without failing transaction if user is not authenticated or audit issue
            \Illuminate\Support\Facades\Log::warning('Audit log failure: ' . $e->getMessage());
        }
    }
}
