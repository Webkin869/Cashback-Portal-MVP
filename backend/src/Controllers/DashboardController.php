<?php
namespace App\Controllers;

use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Services\AuthService;
use PDO;

class DashboardController
{
    private PDO $db;
    private AuthService $auth;

    public function __construct()
    {
        $this->db = Database::connection();
        $this->auth = new AuthService();
    }

    private function user(): array
    {
        $user = $this->auth->userFromToken(Request::bearerToken());
        if (!$user) {
            Response::json(['message' => 'Unauthorized'], 401);
        }
        return $user;
    }

    public function summary(): void
    {
        $user = $this->user();
        $uid = $user['id'];

        $stats = [
            'cashback' => 0,
            'confirmed' => 0,
            'paid' => 0,
            'clicks' => 0,
        ];

        $stmt = $this->db->prepare("SELECT
            COALESCE(SUM(CASE WHEN status IN ('recorded','confirmed','paid') THEN cashback_value ELSE 0 END),0) AS cashback,
            COALESCE(SUM(CASE WHEN status = 'confirmed' THEN cashback_value ELSE 0 END),0) AS confirmed,
            COALESCE(SUM(CASE WHEN status = 'paid' THEN cashback_value ELSE 0 END),0) AS paid
            FROM transactions WHERE user_id = ?");
        $stmt->execute([$uid]);
        $row = $stmt->fetch();
        if ($row) {
            $stats = array_merge($stats, $row);
        }

        $clickStmt = $this->db->prepare('SELECT COUNT(*) AS clicks FROM clicks WHERE user_id = ?');
        $clickStmt->execute([$uid]);
        $stats['clicks'] = (int) ($clickStmt->fetch()['clicks'] ?? 0);

        Response::json(['summary' => $stats]);
    }

    public function transactions(): void
    {
        $user = $this->user();
        $stmt = $this->db->prepare('SELECT t.*, a.title AS action_title FROM transactions t LEFT JOIN actions a ON a.id = t.action_id WHERE t.user_id = ? ORDER BY t.id DESC');
        $stmt->execute([$user['id']]);
        Response::json(['transactions' => $stmt->fetchAll()]);
    }

    public function clicks(): void
    {
        $user = $this->user();
        $stmt = $this->db->prepare('SELECT c.*, a.title AS action_title FROM clicks c LEFT JOIN actions a ON a.id = c.action_id WHERE c.user_id = ? ORDER BY c.id DESC');
        $stmt->execute([$user['id']]);
        Response::json(['clicks' => $stmt->fetchAll()]);
    }

    public function payouts(): void
    {
        $user = $this->user();
        $stmt = $this->db->prepare('SELECT * FROM payout_requests WHERE user_id = ? ORDER BY id DESC');
        $stmt->execute([$user['id']]);
        Response::json(['payouts' => $stmt->fetchAll()]);
    }

    public function referrals(): void
    {
        $user = $this->user();
        $stmt = $this->db->prepare('SELECT r.*, u.name AS referred_name, u.email AS referred_email FROM referrals r LEFT JOIN users u ON u.id = r.referred_user_id WHERE r.referrer_user_id = ? ORDER BY r.id DESC');
        $stmt->execute([$user['id']]);
        Response::json(['referrals' => $stmt->fetchAll(), 'my_code' => $user['referral_code']]);
    }

    public function tickets(): void
    {
        $user = $this->user();
        $stmt = $this->db->prepare('SELECT st.*, a.title AS action_title FROM support_tickets st LEFT JOIN actions a ON a.id = st.action_id WHERE st.user_id = ? ORDER BY st.id DESC');
        $stmt->execute([$user['id']]);
        Response::json(['tickets' => $stmt->fetchAll()]);
    }

    public function createTicket(): void
    {
        $user = $this->user();
        $body = Request::body();
        $subject = trim($body['subject'] ?? 'Nachbuchungsanfrage');
        $actionId = $body['action_id'] ?? null;
        $message = trim($body['message'] ?? '');

        if (!$message) {
            Response::json(['message' => 'Message majburiy'], 422);
        }

        $stmt = $this->db->prepare('INSERT INTO support_tickets (user_id, action_id, subject, message, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, datetime("now"), datetime("now"))');
        $stmt->execute([$user['id'], $actionId, $subject, $message, 'open']);

        Response::json(['message' => 'Ticket yaratildi'], 201);
    }

    public function payoutRequest(): void
    {
        $user = $this->user();
        $body = Request::body();
        $amount = (float) ($body['amount'] ?? 0);
        $provider = trim($body['provider'] ?? 'Amazon');
        $config = require __DIR__ . '/../../config/config.php';
        $min = (float) $config['security']['min_payout'];

        if ($amount < $min) {
            Response::json(['message' => 'Minimum payout 25€'], 422);
        }

        $availableStmt = $this->db->prepare("SELECT COALESCE(SUM(CASE WHEN status='confirmed' THEN cashback_value ELSE 0 END),0) - COALESCE((SELECT SUM(amount) FROM payout_requests WHERE user_id = ? AND status IN ('pending','processing','sent')),0) AS available FROM transactions WHERE user_id = ?");
        $availableStmt->execute([$user['id'], $user['id']]);
        $available = (float) ($availableStmt->fetch()['available'] ?? 0);

        if ($amount > $available) {
            Response::json(['message' => 'Available balance yetarli emas'], 422);
        }

        $stmt = $this->db->prepare('INSERT INTO payout_requests (user_id, amount, provider, payout_type, status, external_payout_id, created_at, updated_at) VALUES (?, ?, ?, ?, ?, NULL, datetime("now"), datetime("now"))');
        $stmt->execute([$user['id'], $amount, $provider, 'voucher', 'pending']);

        Response::json(['message' => 'Payout request yuborildi'], 201);
    }
}
