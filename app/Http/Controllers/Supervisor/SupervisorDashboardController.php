<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;

class SupervisorDashboardController extends Controller
{
    public function dashboard()
    {
        return view('supervisor.dashboard.index');
    }
}
