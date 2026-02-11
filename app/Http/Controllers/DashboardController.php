<?php

namespace App\Http\Controllers;

use App\Services\ClickHouseService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(ClickHouseService $ch)
    {
        if (!$ch->ping()) {
            return view('errors.connection');
        }

        $serverInfo = $ch->getServerInfo();
        $databases = $ch->getDatabases();

        return view('dashboard', compact('serverInfo', 'databases'));
    }
}
