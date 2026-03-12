<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

require_once __DIR__ . '/../src/Core/Database.php';
require_once __DIR__ . '/../src/Core/Response.php';
require_once __DIR__ . '/../src/Core/Request.php';
require_once __DIR__ . '/../src/Core/Jwt.php';
require_once __DIR__ . '/../src/Services/AuthService.php';
require_once __DIR__ . '/../src/Services/CashbackService.php';
require_once __DIR__ . '/../src/Services/FraudService.php';
require_once __DIR__ . '/../src/Controllers/AuthController.php';
require_once __DIR__ . '/../src/Controllers/ActionController.php';
require_once __DIR__ . '/../src/Controllers/DashboardController.php';
require_once __DIR__ . '/../src/Controllers/PostbackController.php';
require_once __DIR__ . '/../src/Controllers/AdminController.php';

spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    if (str_starts_with($class, $prefix)) {
        $relative = str_replace('App\\', '', $class);
        $path = __DIR__ . '/../src/' . str_replace('\\', '/', $relative) . '.php';
        if (file_exists($path)) {
            require_once $path;
        }
    }
});

use App\Controllers\ActionController;
use App\Controllers\AdminController;
use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\PostbackController;
use App\Core\Response;

$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$auth = new AuthController();
$actions = new ActionController();
$dashboard = new DashboardController();
$postback = new PostbackController();
$admin = new AdminController();

if ($method === 'GET' && $uri === '/') {
    Response::json(['message' => 'Cashback Portal API']);
}

if ($method === 'POST' && $uri === '/api/auth/register') $auth->register();
if ($method === 'POST' && $uri === '/api/auth/login') $auth->login();
if ($method === 'GET' && $uri === '/api/auth/me') $auth->me();
if ($method === 'POST' && $uri === '/api/auth/forgot-password') $auth->forgotPassword();

if ($method === 'GET' && $uri === '/api/actions') $actions->index();
if ($method === 'GET' && preg_match('#^/api/actions/([a-zA-Z0-9\-_]+)$#', $uri, $m)) $actions->show($m[1]);
if ($method === 'POST' && preg_match('#^/api/actions/(\d+)/click$#', $uri, $m)) $actions->click((int)$m[1]);

if ($method === 'GET' && $uri === '/api/dashboard/summary') $dashboard->summary();
if ($method === 'GET' && $uri === '/api/dashboard/transactions') $dashboard->transactions();
if ($method === 'GET' && $uri === '/api/dashboard/clicks') $dashboard->clicks();
if ($method === 'GET' && $uri === '/api/dashboard/payouts') $dashboard->payouts();
if ($method === 'GET' && $uri === '/api/dashboard/referrals') $dashboard->referrals();
if ($method === 'GET' && $uri === '/api/dashboard/tickets') $dashboard->tickets();
if ($method === 'POST' && $uri === '/api/dashboard/tickets') $dashboard->createTicket();
if ($method === 'POST' && $uri === '/api/dashboard/payouts/request') $dashboard->payoutRequest();

if ($method === 'GET' && $uri === '/postback/awin') $postback->awin();

if ($method === 'GET' && $uri === '/api/admin/actions') $admin->actions();
if ($method === 'POST' && $uri === '/api/admin/actions') $admin->createAction();
if ($method === 'GET' && $uri === '/api/admin/users') $admin->users();
if ($method === 'GET' && $uri === '/api/admin/transactions') $admin->transactions();
if ($method === 'PATCH' && preg_match('#^/api/admin/transactions/(\d+)/status$#', $uri, $m)) $admin->updateTransactionStatus((int)$m[1]);
if ($method === 'GET' && $uri === '/api/admin/payouts') $admin->payouts();
if ($method === 'GET' && $uri === '/api/admin/tickets') $admin->tickets();

Response::json(['message' => 'Route not found'], 404);
