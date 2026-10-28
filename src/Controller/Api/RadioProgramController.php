<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Http\Exception\ValidationException;
use App\Http\JsonResponse;
use App\Http\Request;
use App\Repository\RadioProgramRepository;
use App\Support\Clock;
use DateTimeImmutable;
use DateTimeZone;

final class RadioProgramController
{
    public function __construct(
        private RadioProgramRepository $programs,
        private Clock $clock,
        private DateTimeZone $timezone,
    ) {
    }

    /**
     * Optional ?radio=<station id> (default 1). data = null when no program is active.
     *
     * @param array<string, string> $params
     */
    public function index(Request $request, array $params): JsonResponse
    {
        $radio = $request->query('radio', '1') ?? '1';
        if (preg_match('/^[1-9]\d{0,5}$/', $radio) !== 1) {
            throw new ValidationException('invalid_radio', 'Radio must be a positive integer.');
        }
        $today = (new DateTimeImmutable('@' . $this->clock->now()))->setTimezone($this->timezone)->format('Y-m-d');
        return JsonResponse::ok($this->programs->currentSchedule((int) $radio, $today));
    }
}
