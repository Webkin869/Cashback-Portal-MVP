<?php
namespace App\Controllers;

use App\Core\Database;
use App\Core\Response;
use App\Services\CashbackService;
use PDO;

class PostbackController
{
    private PDO $db;
    private CashbackService $cashback;

    public function __construct()
    {
        $this->db = Database::connection();
        $this->cashback = new CashbackService();
    }

    public function awin(): void
    {
        $clickToken = $_GET['click_token'] ?? '';
        $externalId = $_GET['transaction_id'] ?? '';
        $saleAmount = (float) ($_GET['sale_amount'] ?? 0);
        $commission = (float) ($_GET['commission_amount'] ?? 0);
        $status = $_GET['status'] ?? 'pending';

        if (!$clickToken || !$externalId) {
            Response::json(['message' => 'Missing click_token or transaction_id'], 422);
        }

        $dup = $this->db->prepare('SELECT id FROM transactions WHERE external_transaction_id = ? LIMIT 1');
        $dup->execute([$externalId]);
        if ($dup->fetch()) {
            Response::json(['message' => 'Already processed']);
        }

        $clickStmt = $this->db->prepare('SELECT c.*, a.cashback_type, a.cashback_value FROM clicks c JOIN actions a ON a.id = c.action_id WHERE c.click_token = ? LIMIT 1');
        $clickStmt->execute([$clickToken]);
        $click = $clickStmt->fetch();
        if (!$click) {
            Response::json(['message' => 'Click topilmadi'], 404);
        }

        $cashbackValue = $this->cashback->calculate($click['cashback_type'], (float) $click['cashback_value'], $saleAmount);
        $mappedStatus = match ($status) {
            'approved', 'confirmed' => 'confirmed',
            'declined', 'rejected' => 'rejected',
            'paid' => 'paid',
            default => 'recorded',
        };

        $insert = $this->db->prepare('INSERT INTO transactions (user_id, action_id, click_id, network, external_transaction_id, order_value, commission_value, cashback_value, status, tracked_at, confirmed_at, paid_at, meta_json, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, datetime("now"), CASE WHEN ? = "confirmed" THEN datetime("now") ELSE NULL END, CASE WHEN ? = "paid" THEN datetime("now") ELSE NULL END, ?, datetime("now"), datetime("now"))');
        $insert->execute([
            $click['user_id'],
            $click['action_id'],
            $click['id'],
            'awin',
            $externalId,
            $saleAmount,
            $commission,
            $cashbackValue,
            $mappedStatus,
            $mappedStatus,
            $mappedStatus,
            json_encode($_GET),
        ]);

        Response::json(['message' => 'Postback processed', 'cashback' => $cashbackValue]);
    }
}
