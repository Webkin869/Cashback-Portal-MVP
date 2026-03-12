<?php
namespace App\Controllers;

use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Services\AuthService;
use PDO;

class ActionController
{
    private PDO $db;
    private AuthService $auth;

    public function __construct()
    {
        $this->db = Database::connection();
        $this->auth = new AuthService();
    }

    public function index(): void
    {
        $stmt = $this->db->query('SELECT id, title, slug, short_description, cashback_type, cashback_value, partner_network, banner_image, is_featured FROM actions WHERE is_active = 1 ORDER BY is_featured DESC, sort_order ASC, id DESC');
        Response::json(['actions' => $stmt->fetchAll()]);
    }

    public function show(string $slug): void
    {
        $stmt = $this->db->prepare('SELECT * FROM actions WHERE slug = ? AND is_active = 1 LIMIT 1');
        $stmt->execute([$slug]);
        $action = $stmt->fetch();
        if (!$action) {
            Response::json(['message' => 'Aktion topilmadi'], 404);
        }
        Response::json(['action' => $action]);
    }

    public function click(int $id): void
    {
        $user = $this->auth->userFromToken(Request::bearerToken());
        if (!$user) {
            Response::json(['message' => 'Login required'], 401);
        }

        $stmt = $this->db->prepare('SELECT * FROM actions WHERE id = ? AND is_active = 1 LIMIT 1');
        $stmt->execute([$id]);
        $action = $stmt->fetch();
        if (!$action) {
            Response::json(['message' => 'Aktion topilmadi'], 404);
        }

        $token = bin2hex(random_bytes(16));
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $referrer = $_SERVER['HTTP_REFERER'] ?? null;

        $insert = $this->db->prepare('INSERT INTO clicks (user_id, action_id, click_token, ip_address, user_agent, referrer, created_at) VALUES (?, ?, ?, ?, ?, ?, datetime("now"))');
        $insert->execute([$user['id'], $id, $token, $ip, $ua, $referrer]);

        $join = str_contains($action['tracking_url'], '?') ? '&' : '?';
        $redirectUrl = $action['tracking_url'] . $join . 'click_token=' . urlencode($token) . '&user_id=' . urlencode((string) $user['id']);

        Response::json([
            'message' => 'Click recorded',
            'click_token' => $token,
            'redirect_url' => $redirectUrl,
        ]);
    }
}
