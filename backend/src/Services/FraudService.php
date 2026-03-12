<?php
namespace App\Services;

use App\Core\Database;
use PDO;

class FraudService
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    public function flagIfNeeded(int $userId, string $reason, string $severity = 'medium'): void
    {
        $stmt = $this->db->prepare('INSERT INTO fraud_flags (user_id, reason, severity, created_at) VALUES (?, ?, ?, datetime("now"))');
        $stmt->execute([$userId, $reason, $severity]);
    }

    public function preventSelfReferral(int $userId, ?int $referredBy): bool
    {
        return $referredBy !== null && $userId === $referredBy;
    }
}
