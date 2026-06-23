<?php

namespace App\Http\Controllers\Api;

use App\Application\Link\Handler\GetLinkStatisticsHandler;
use App\Domain\Link\Exception\ShortLinkNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Resources\LinkStatisticsResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StatisticsController extends Controller
{
    public function __construct(
        private GetLinkStatisticsHandler $statisticsHandler,
    ) {
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $days = (int) $request->query('days', 30);
        $days = max(1, min($days, 90));

        try {
            $stats = $this->statisticsHandler->handle($id, (int) $request->user()->id, $days);
        } catch (ShortLinkNotFoundException) {
            return response()->json(['message' => 'Короткая ссылка не найдена'], 404);
        }

        return (new LinkStatisticsResource($stats))->response();
    }
}
