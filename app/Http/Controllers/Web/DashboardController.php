<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\Web\DashboardSevice;
use Inertia\Inertia;

class DashboardController extends Controller
{
    //
    protected $dashboardService;

    public function __construct(DashboardSevice $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function index()
    {

        $stats = $this->dashboardService->getStats();

        return Inertia::render('Dashboard', [
            'stats' => $stats]);
    }
}
