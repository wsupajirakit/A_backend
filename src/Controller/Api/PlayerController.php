<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Http\Exception\NotFoundException;
use App\Http\JsonResponse;
use App\Http\Request;
use App\Repository\PlayerRepository;

final class PlayerController
{
    public function __construct(private PlayerRepository $player)
    {
    }

    /** @param array<string, string> $params */
    public function show(Request $request, array $params): JsonResponse
    {
        $data = $this->player->player();
        if ($data === null) {
            throw new NotFoundException('Stream not configured.');
        }
        return JsonResponse::ok($data);
    }

    /** @param array<string, string> $params */
    public function appData(Request $request, array $params): JsonResponse
    {
        return JsonResponse::ok($this->player->appDataWithPopups());
    }
}
