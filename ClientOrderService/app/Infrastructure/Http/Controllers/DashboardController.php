<?php

namespace App\Infrastructure\Http\Controllers;

use App\Application\Dashboard\Handlers\GetDashboardStatsHandler;
use App\Application\Dashboard\Queries\GetDashboardStatsQuery;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class DashboardController extends Controller
{
    public function stats(Request $request, GetDashboardStatsHandler $handler): JsonResponse
    {
        $query = new GetDashboardStatsQuery();
        $stats = $handler->handle($query);

        return new JsonResponse($stats);
    }
}
