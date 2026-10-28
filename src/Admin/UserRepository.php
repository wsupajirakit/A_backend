<?php

declare(strict_types=1);

namespace App\Admin;

use App\Database\Connection;

final class UserRepository
{
    public function __construct(private Connection $db)
    {
    }

    /** @return array<string, mixed>|null */
    public function findByUsername(string $username): ?array
    {
        $stmt = $this->db->pdo()->prepare('SELECT * FROM user WHERE username = :u LIMIT 1');
        $stmt->execute([':u' => $username]);
        $row = $stmt->fetch();
        return is_array($row) ? $row : null;
    }

    /** @return array<string, mixed>|null */
    public function findActiveById(int $id): ?array
    {
        $stmt = $this->db->pdo()->prepare('SELECT * FROM user WHERE user_id = :id AND active_status = 1 LIMIT 1');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return is_array($row) ? $row : null;
    }

    public function updatePasswordHash(int $id, string $hash): void
    {
        $stmt = $this->db->pdo()->prepare('UPDATE user SET password = :p WHERE user_id = :id');
        $stmt->execute([':p' => $hash, ':id' => $id]);
    }
}
