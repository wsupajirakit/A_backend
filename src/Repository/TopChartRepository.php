<?php

declare(strict_types=1);

namespace App\Repository;

use App\Database\Connection;
use App\Support\ThaiDate;

final class TopChartRepository
{
    public function __construct(private Connection $db)
    {
    }

    /**
     * Every chart with its number-one song (oldest first, as before).
     *
     * @return list<array<string, mixed>>
     */
    public function listWithNumberOne(): array
    {
        $sql = 'SELECT tc.top_chart_date, s.song_name, s.artist_name_map
                FROM top_chart tc
                LEFT JOIN top_chart_song tcs ON tcs.top_chart_id = tc.top_chart_id AND tcs.song_order = 1
                LEFT JOIN song s ON s.song_id = tcs.song_id
                ORDER BY tc.top_chart_date ASC';
        $out = [];
        foreach ($this->db->pdo()->query($sql)->fetchAll() as $row) {
            $out[] = [
                'num_1_song' => $row['song_name'],
                'num_1_songs_artist' => $row['artist_name_map'],
                'date' => ThaiDate::dmy((string) $row['top_chart_date']),
            ];
        }
        return $out;
    }

    /**
     * Distinct chart dates, newest first.
     *
     * @return list<array{key: string, label: string, date: string}>
     */
    public function dates(): array
    {
        $sql = 'SELECT DISTINCT top_chart_date FROM top_chart ORDER BY top_chart_date DESC';
        $out = [];
        foreach ($this->db->pdo()->query($sql)->fetchAll() as $row) {
            $ymd = (string) $row['top_chart_date'];
            $out[] = ['key' => ThaiDate::dmy($ymd), 'label' => ThaiDate::dmy($ymd), 'date' => $ymd];
        }
        return $out;
    }

    /**
     * One chart by date (Y-m-d), or null when there is none.
     *
     * @return array{songs: list<array<string, mixed>>, date_formatted: string, date: string}|null
     */
    public function byDate(string $ymd): ?array
    {
        $pdo = $this->db->pdo();
        $stmt = $pdo->prepare('SELECT top_chart_id, top_chart_date FROM top_chart WHERE top_chart_date = :d LIMIT 1');
        $stmt->execute([':d' => $ymd]);
        $chart = $stmt->fetch();
        if ($chart === false) {
            return null;
        }
        $stmt = $pdo->prepare(
            'SELECT tcs.song_order, tcs.last_week_order, tcs.on_chart_week_number, s.song_name, s.artist_name_map
             FROM top_chart_song tcs
             INNER JOIN song s ON s.song_id = tcs.song_id
             WHERE tcs.top_chart_id = :id
             ORDER BY tcs.song_order ASC'
        );
        $stmt->execute([':id' => $chart['top_chart_id']]);
        $songs = [];
        foreach ($stmt->fetchAll() as $row) {
            $songs[] = [
                'num' => $row['song_order'],
                'name' => $row['song_name'],
                'artists' => $row['artist_name_map'],
                'lastChartNum' => empty($row['last_week_order']) ? '' : $row['last_week_order'],
                'onChartLong' => empty($row['on_chart_week_number']) ? '' : $row['on_chart_week_number'],
            ];
        }
        $date = (string) $chart['top_chart_date'];
        return [
            'songs' => $songs,
            'date_formatted' => ThaiDate::readable($date),
            'date' => ThaiDate::dmy($date),
        ];
    }
}
