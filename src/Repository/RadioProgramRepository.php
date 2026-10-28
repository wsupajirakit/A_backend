<?php

declare(strict_types=1);

namespace App\Repository;

use App\Database\Connection;

final class RadioProgramRepository
{
    public function __construct(private Connection $db, private string $presenterImageBaseUrl)
    {
    }

    /**
     * Weekly schedule of the program active on $today for a station:
     * [day => [{dj: [{name, image}], time: {start, end}}]], or null when none.
     *
     * @return array<int, list<array<string, mixed>>>|null
     */
    public function currentSchedule(int $radioId, string $today): ?array
    {
        $pdo = $this->db->pdo();
        $stmt = $pdo->prepare(
            'SELECT rdo_program_id FROM radio_program
             WHERE :td BETWEEN rdo_program_start AND rdo_program_end AND radio_id = :rdo
             ORDER BY rdo_program_start DESC LIMIT 1'
        );
        $stmt->execute([':td' => $today, ':rdo' => $radioId]);
        $programId = $stmt->fetchColumn();
        if ($programId === false) {
            return null;
        }

        $stmt = $pdo->prepare(
            'SELECT d.rdo_program_details_id AS id, d.rdo_program_details_day AS day,
                    d.rdo_program_details_time_start AS t_start, d.rdo_program_details_time_end AS t_end,
                    rp.rp_name, rp.rp_image
             FROM radio_program_details d
             LEFT JOIN radio_program_details_radio_presenter dp ON dp.rdo_program_details_id = d.rdo_program_details_id
             LEFT JOIN radio_presenter rp ON rp.rp_id = dp.rp_id
             WHERE d.rdo_program_id = :id
             ORDER BY d.rdo_program_details_time_start ASC, d.rdo_program_details_id ASC'
        );
        $stmt->execute([':id' => $programId]);

        $slots = [];
        foreach ($stmt->fetchAll() as $row) {
            $id = (int) $row['id'];
            if (!isset($slots[$id])) {
                $end = new \DateTimeImmutable((string) $row['t_end']);
                $slots[$id] = [
                    'day' => (int) $row['day'],
                    'dj' => [],
                    'time' => [
                        'start' => (new \DateTimeImmutable((string) $row['t_start']))->format('H:i'),
                        'end' => $end->modify('+1 minute')->format('H:i'),
                    ],
                ];
            }
            if ($row['rp_name'] !== null) {
                $slots[$id]['dj'][] = ['name' => $row['rp_name'], 'image' => $this->presenterImageBaseUrl . $row['rp_image']];
            }
        }

        $schedule = [];
        foreach ($slots as $slot) {
            $schedule[$slot['day']][] = ['dj' => $slot['dj'], 'time' => $slot['time']];
        }
        return $schedule;
    }
}
