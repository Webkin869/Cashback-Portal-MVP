DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS actions;
DROP TABLE IF EXISTS clicks;
DROP TABLE IF EXISTS transactions;
DROP TABLE IF EXISTS wallet_entries;
DROP TABLE IF EXISTS payout_requests;
DROP TABLE IF EXISTS referrals;
DROP TABLE IF EXISTS support_tickets;
DROP TABLE IF EXISTS fraud_flags;

CREATE TABLE users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    referral_code VARCHAR(50) UNIQUE,
    referred_by_user_id INTEGER NULL,
    role VARCHAR(50) NOT NULL DEFAULT 'user',
    status VARCHAR(50) NOT NULL DEFAULT 'active',
    created_at DATETIME NULL,
    updated_at DATETIME NULL,
    last_login_at DATETIME NULL
);

CREATE TABLE actions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    short_description TEXT,
    description TEXT,
    terms TEXT,
    cashback_type VARCHAR(20) NOT NULL,
    cashback_value DECIMAL(10,2) NOT NULL DEFAULT 0,
    partner_network VARCHAR(100),
    tracking_url TEXT,
    banner_image TEXT,
    is_featured INTEGER NOT NULL DEFAULT 0,
    is_active INTEGER NOT NULL DEFAULT 1,
    sort_order INTEGER NOT NULL DEFAULT 0,
    created_at DATETIME NULL,
    updated_at DATETIME NULL
);

CREATE TABLE clicks (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    action_id INTEGER NOT NULL,
    click_token VARCHAR(255) NOT NULL UNIQUE,
    ip_address VARCHAR(64),
    user_agent TEXT,
    referrer TEXT,
    created_at DATETIME NULL
);

CREATE TABLE transactions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    action_id INTEGER NOT NULL,
    click_id INTEGER NULL,
    network VARCHAR(100),
    external_transaction_id VARCHAR(255) UNIQUE,
    order_value DECIMAL(10,2) DEFAULT 0,
    commission_value DECIMAL(10,2) DEFAULT 0,
    cashback_value DECIMAL(10,2) DEFAULT 0,
    status VARCHAR(50) NOT NULL DEFAULT 'recorded',
    tracked_at DATETIME NULL,
    confirmed_at DATETIME NULL,
    paid_at DATETIME NULL,
    meta_json TEXT,
    created_at DATETIME NULL,
    updated_at DATETIME NULL
);

CREATE TABLE wallet_entries (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    transaction_id INTEGER NULL,
    type VARCHAR(50) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    status VARCHAR(50) NOT NULL,
    note TEXT,
    created_at DATETIME NULL
);

CREATE TABLE payout_requests (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    provider VARCHAR(100) NOT NULL,
    payout_type VARCHAR(50) NOT NULL,
    status VARCHAR(50) NOT NULL,
    external_payout_id VARCHAR(255) NULL,
    created_at DATETIME NULL,
    updated_at DATETIME NULL
);

CREATE TABLE referrals (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    referrer_user_id INTEGER NOT NULL,
    referred_user_id INTEGER NOT NULL,
    signup_bonus DECIMAL(10,2) DEFAULT 0,
    cashback_share_percent DECIMAL(10,2) DEFAULT 0,
    is_eligible INTEGER NOT NULL DEFAULT 1,
    created_at DATETIME NULL
);

CREATE TABLE support_tickets (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    action_id INTEGER NULL,
    subject VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    status VARCHAR(50) NOT NULL DEFAULT 'open',
    created_at DATETIME NULL,
    updated_at DATETIME NULL
);

CREATE TABLE fraud_flags (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    reason TEXT NOT NULL,
    severity VARCHAR(50) NOT NULL,
    created_at DATETIME NULL
);

INSERT INTO users (name, email, password, referral_code, role, status, created_at, updated_at)
VALUES
('Admin', 'admin@example.com', '$2y$12$4koT5Zoac7d//RAwlGrzs.Na8/e0O3ZjPgRlYf3UW0jp7YyPguou2', 'ADMIN001', 'admin', 'active', datetime('now'), datetime('now')),
('Demo User', 'user@example.com', '$2y$12$4koT5Zoac7d//RAwlGrzs.Na8/e0O3ZjPgRlYf3UW0jp7YyPguou2', 'USER001', 'user', 'active', datetime('now'), datetime('now'));

INSERT INTO actions (title, slug, short_description, description, terms, cashback_type, cashback_value, partner_network, tracking_url, banner_image, is_featured, is_active, sort_order, created_at, updated_at)
VALUES
('DKB Girokonto', 'dkb-girokonto', '20€ Cashback für Kontoeröffnung', 'Eröffne ein Girokonto und erhalte Cashback.', 'Nur Neukunden, vollständige Verifizierung erforderlich.', 'fixed', 20.00, 'awin', 'https://example.com/affiliate/dkb', 'https://images.unsplash.com/photo-1556740749-887f6717d7e4', 1, 1, 1, datetime('now'), datetime('now')),
('Nike Shop', 'nike-shop', '5% Cashback auf Einkäufe', 'Kaufe bei Nike und erhalte prozentuales Cashback.', 'Kein Cashback auf Geschenkkarten.', 'percent', 5.00, 'financeads', 'https://example.com/affiliate/nike', 'https://images.unsplash.com/photo-1542291026-7eec264c27ff', 1, 1, 2, datetime('now'), datetime('now')),
('Amazon Prime', 'amazon-prime', '15€ Cashback für Testabo', 'Melde dich für Prime an und sichere dir eine Prämie.', 'Nur einmal pro Haushalt.', 'fixed', 15.00, 'communicationads', 'https://example.com/affiliate/prime', 'https://images.unsplash.com/photo-1523475472560-d2df97ec485c', 0, 1, 3, datetime('now'), datetime('now'));

INSERT INTO clicks (user_id, action_id, click_token, ip_address, user_agent, referrer, created_at)
VALUES (2, 1, 'demo-click-token-111', '127.0.0.1', 'Demo UA', 'http://localhost', datetime('now'));

INSERT INTO transactions (user_id, action_id, click_id, network, external_transaction_id, order_value, commission_value, cashback_value, status, tracked_at, confirmed_at, paid_at, meta_json, created_at, updated_at)
VALUES
(2, 1, 1, 'awin', 'TX-DEMO-001', 0, 25.00, 20.00, 'confirmed', datetime('now'), datetime('now'), NULL, '{}', datetime('now'), datetime('now')),
(2, 2, 1, 'financeads', 'TX-DEMO-002', 120.00, 8.00, 6.00, 'recorded', datetime('now'), NULL, NULL, '{}', datetime('now'), datetime('now'));

INSERT INTO payout_requests (user_id, amount, provider, payout_type, status, external_payout_id, created_at, updated_at)
VALUES (2, 25.00, 'Amazon', 'voucher', 'sent', 'TREM-001', datetime('now'), datetime('now'));

INSERT INTO referrals (referrer_user_id, referred_user_id, signup_bonus, cashback_share_percent, is_eligible, created_at)
VALUES (1, 2, 5.00, 10.00, 1, datetime('now'));

INSERT INTO support_tickets (user_id, action_id, subject, message, status, created_at, updated_at)
VALUES (2, 1, 'Nachbuchungsanfrage', 'Meine Transaktion wurde nicht korrekt getrackt.', 'open', datetime('now'), datetime('now'));
