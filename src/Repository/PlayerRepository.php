<?php

declare(strict_types=1);

namespace App\Repository;

use App\Database\Connection;

/**
 * Station/app data from admin/data/app-data.json plus app popups from the DB.
 * The stream URL comes from configuration (STREAM_URL), never from code.
 */
final class PlayerRepository
{
    public const STATION_KEY = 'aradio';

    public function __construct(
        private string $appDataPath,
        private string $streamUrl,
        private Connection $db,
        private string $popupImageBaseUrl,
    ) {
    }

    /** @return array<string, mixed> */
    public function appData(): array
    {
        if (!is_readable($this->appDataPath)) {
            return [];
        }
        $data = json_decode((string) file_get_contents($this->appDataPath), true);
        return is_array($data) ? $data : [];
    }

    /**
     * {title, radio: {aradio: {streaming_url, straming_url, frequency, wave, sms_number}}} or null.
     * "straming_url" is the legacy field name, kept for older app builds.
     *
     * @return array<string, mixed>|null
     */
    public function player(): ?array
    {
        $appData = $this->appData();
        $station = $this->station($appData);
        $streamUrl = $this->streamUrl !== '' ? $this->streamUrl : (string) ($station['straming_url'] ?? '');
        if ($streamUrl === '') {
            return null;
        }
        $appName = (string) ($appData['about']['app_name'] ?? '');
        return [
            'title' => $appName !== '' ? $appName . ' Stream' : 'Radio Stream',
            'radio' => [
                self::STATION_KEY => [
                    'streaming_url' => $streamUrl,
                    'straming_url' => $streamUrl,
                    'frequency' => $station['frequency'] ?? null,
                    'wave' => $station['wave'] ?? null,
                    'sms_number' => $station['sms_number'] ?? null,
                ],
            ],
        ];
    }

    /**
     * Former webservice/app/appData.php result: app-data.json + popups.
     *
     * @return array<string, mixed>
     */
    public function appDataWithPopups(): array
    {
        $data = $this->appData();
        if ($this->streamUrl !== '' && is_array($data['radio'] ?? null)) {
            foreach (array_keys($data['radio']) as $key) {
                $data['radio'][$key]['straming_url'] = $this->streamUrl;
            }
        }
        $data['popup'] = $this->popups();
        return $data;
    }

    /** @return list<array<string, mixed>> */
    private function popups(): array
    {
        $stmt = $this->db->pdo()->prepare(
            'SELECT pr_image AS image, pr_title AS title, pr_url AS link FROM public_relations WHERE pr_type = :type'
        );
        $stmt->execute([':type' => 4]);
        $popups = [];
        foreach ($stmt->fetchAll() as $row) {
            $row['image'] = $this->popupImageBaseUrl . $row['image'];
            $popups[] = $row;
        }
        return $popups;
    }

    /**
     * @param array<string, mixed> $appData
     * @return array<string, mixed>
     */
    private function station(array $appData): array
    {
        $stations = is_array($appData['radio'] ?? null) ? $appData['radio'] : [];
        $station = $stations[self::STATION_KEY] ?? reset($stations);
        return is_array($station) ? $station : [];
    }
}
