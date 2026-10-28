<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Http\Exception\NotFoundException;
use App\Http\Exception\ValidationException;
use App\Http\JsonResponse;
use App\Http\Request;
use App\Repository\TopChartRepository;
use App\Support\ThaiDate;

final class TopChartController
{
    public function __construct(private TopChartRepository $charts)
    {
    }

    /** @param array<string, string> $params */
    public function index(Request $request, array $params): JsonResponse
    {
        return JsonResponse::ok($this->charts->listWithNumberOne());
    }

    /** @param array<string, string> $params */
    public function dates(Request $request, array $params): JsonResponse
    {
        return JsonResponse::ok($this->charts->dates());
    }

    /** @param array<string, string> $params */
    public function show(Request $request, array $params): JsonResponse
    {
        $date = $params['date'] ?? '';
        if (!ThaiDate::isValidYmd($date)) {
            throw new ValidationException('invalid_date', 'Date must be a valid YYYY-MM-DD.');
        }
        $chart = $this->charts->byDate($date);
        if ($chart === null) {
            throw new NotFoundException('No top chart for this date.');
        }
        return JsonResponse::ok($chart);
    }
}
