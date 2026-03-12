<?php
namespace App\Services;

use App\Core\Database;
use App\Core\Jwt;
use PDO;

class AuthService
{
    private PDO $db;
    private array $config;

    public function __construct()
    {
        $this->db = Database::connection();
        $this->config = require __DIR__ . '/../../config/config.php';
    }

    public function register(array $data): array
    {
        $email = strtolower(trim($data['email'] ?? ''));
        $password = $data['password'] ?? '';
        $name = trim($data['name'] ?? '');
        $referralCode = trim($data['referral_code'] ?? '');

        if (!$name || !$email || !$password) {
            return ['error' => 'Name, email va password majburiy'];
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['error' => 'Email noto‘g‘ri'];
        }

        if (strlen($password) < 8) {
            return ['error' => 'Password kamida 8 ta belgi bo‘lishi kerak'];
        }

        $stmt = $this->db->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            return ['error' => 'Bu email allaqachon mavjud'];
        }

        $referredBy = null;
        if ($referralCode) {
            $refStmt = $this->db->prepare('SELECT id FROM users WHERE referral_code = ?');
            $refStmt->execute([$referralCode]);
            $refUser = $refStmt->fetch();
            if ($refUser) {
                $referredBy = $refUser['id'];
            }
        }

        $myCode = strtoupper(substr(bin2hex(random_bytes(6)), 0, 8));
        $hash = password_hash($password, $this->config['security']['password_algo']);

        $insert = $this->db->prepare('INSERT INTO users (name, email, password, referral_code, referred_by_user_id, role, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, datetime("now"), datetime("now"))');
        $insert->execute([$name, $email, $hash, $myCode, $referredBy, 'user', 'active']);
        $userId = (int) $this->db->lastInsertId();

        if ($referredBy && $referredBy !== $userId) {
            $bonus = 5;
            $refInsert = $this->db->prepare('INSERT INTO referrals (referrer_user_id, referred_user_id, signup_bonus, cashback_share_percent, is_eligible, created_at) VALUES (?, ?, ?, ?, ?, datetime("now"))');
            $refInsert->execute([$referredBy, $userId, $bonus, 10, 1]);

            $wallet = $this->db->prepare('INSERT INTO wallet_entries (user_id, transaction_id, type, amount, status, note, created_at) VALUES (?, NULL, ?, ?, ?, ?, datetime("now"))');
            $wallet->execute([$referredBy, 'referral_bonus', $bonus, 'confirmed', 'Referral signup bonus']);
            $wallet->execute([$userId, 'referral_bonus', $bonus, 'confirmed', 'Welcome referral bonus']);
        }

        return $this->login($email, $password);
    }

    public function login(string $email, string $password): array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email = ? AND status = ? LIMIT 1');
        $stmt->execute([strtolower(trim($email)), 'active']);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password'])) {
            return ['error' => 'Login yoki parol xato'];
        }

        $token = Jwt::encode([
            'sub' => (int) $user['id'],
            'role' => $user['role'],
            'exp' => time() + (60 * 60 * 24 * 7),
        ], $this->config['security']['jwt_secret']);

        $update = $this->db->prepare('UPDATE users SET last_login_at = datetime("now") WHERE id = ?');
        $update->execute([$user['id']]);

        unset($user['password']);
        return ['token' => $token, 'user' => $user];
    }

    public function userFromToken(?string $token): ?array
    {
        if (!$token) {
            return null;
        }

        $payload = Jwt::decode($token, $this->config['security']['jwt_secret']);
        if (!$payload || empty($payload['sub'])) {
            return null;
        }

        $stmt = $this->db->prepare('SELECT id, name, email, referral_code, referred_by_user_id, role, status, created_at, last_login_at FROM users WHERE id = ? LIMIT 1');
        $stmt->execute([(int) $payload['sub']]);
        $user = $stmt->fetch();
        return $user ?: null;
    }
}
