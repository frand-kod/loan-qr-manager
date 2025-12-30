<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class LogService
{
    public function record($activity, $modelType = null, $modelId = null)
    {
        return ActivityLog::create([
            'user_id' => Auth::id() ?? 1, // Mengambil ID user yang sedang login (Sanctum)
            'activity' => $activity,
            'model_type' => $modelType,
            'model_id' => $modelId,
            'created_at' => now(),
        ]);
    }

    public function getRecentLogs($userId, $limit = 5)
    {
        return ActivityLog::where('user_id', $userId)
            ->latest()
            ->limit($limit)
            ->get();
    }
}
