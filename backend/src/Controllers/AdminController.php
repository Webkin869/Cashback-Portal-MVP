<?php
namespace App\Controllers;

use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Services\AuthService;
use PDO;

class AdminController
{
    private PDO $db;
    private AuthService $auth;

    public function __construct()
    {
        $this->db = Database::connection();
        $this->auth = new AuthService();
    }

    private function admin(): array
    {
        $user = $this->auth->userFromToken(Request::bearerToken());
        if (!$user || $user['role'] !== 'admin') {
            Response::json(['message' => 'Admin only'], 403);
        }
        return $user;
    }

    public function actions(): void
    {
        $this->admin();
        $stmt = $this->db->query('SELECT * FROM actions ORDER BY id DESC');
        Response::json(['actions' => $stmt->fetchAll()]);
    }

    public function createAction(): void
    {
        $this->admin();
        $b = Request::body();
        $stmt = $this->db->prepare('INSERT INTO actions (title, slug, short_description, description, terms, cashback_type, cashback_value, partner_network, tracking_url, banner_image, is_featured, is_active, sort_order, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, datetime("now"), datetime("now"))');
        $stmt->execute([
            $b['title'] ?? '',
            $b['slug'] ?? '',
            $b['short_description'] ?? '',
            $b['description'] ?? '',
            $b['terms'] ?? '',
            $b['cashback_type'] ?? 'fixed',
            $b['cashback_value'] ?? 0,
            $b['partner_network'] ?? 'awin',
            $b['tracking_url'] ?? '',
            $b['banner_image'] ?? '',
            !empty($b['is_featured']) ? 1 : 0,
            !empty($b['is_active']) ? 1 : 0,
            $b['sort_order'] ?? 0,
        ]);
        Response::json(['message' => 'Action created'], 201);
    }

    public function users(): void
    {
        $this->admin();
        $stmt = $this->db->query('SELECT id, name, email, referral_code, role, status, created_at, last_login_at FROM users ORDER BY id DESC');
        Response::json(['users' => $stmt->fetchAll()]);
    }

    public function transactions(): void
    {
        $this->admin();
        $stmt = $this->db->query('SELECT t.*, u.email, a.title AS action_title FROM transactions t LEFT JOIN users u ON u.id = t.user_id LEFT JOIN actions a ON a.id = t.action_id ORDER BY t.id DESC');
        Response::json(['transactions' => $stmt->fetchAll()]);
    }

    public function updateTransactionStatus(int $id): void
    {
        $this->admin();
        $b = Request::body();
        $status = $b['status'] ?? 'recorded';
        $stmt = $this->db->prepare('UPDATE transactions SET status = ?, confirmed_at = CASE WHEN ? = "confirmed" THEN datetime("now") ELSE confirmed_at END, paid_at = CASE WHEN ? = "paid" THEN datetime("now") ELSE paid_at END, updated_at = datetime("now") WHERE id = ?');
        $stmt->execute([$status, $status, $status, $id]);
        Response::json(['message' => 'Transaction status updated']);
    }

    public function payouts(): void
    {
        $this->admin();
        $stmt = $this->db->query('SELECT p.*, u.email FROM payout_requests p LEFT JOIN users u ON u.id = p.user_id ORDER BY p.id DESC');
        Response::json(['payouts' => $stmt->fetchAll()]);
    }

    public function tickets(): void
    {
        $this->admin();
        $stmt = $this->db->query('SELECT st.*, u.email, a.title AS action_title FROM support_tickets st LEFT JOIN users u ON u.id = st.user_id LEFT JOIN actions a ON a.id = st.action_id ORDER BY st.id DESC');
        Response::json(['tickets' => $stmt->fetchAll()]);
    }
}
