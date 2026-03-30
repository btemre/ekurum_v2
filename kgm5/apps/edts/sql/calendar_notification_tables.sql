-- Takvim bildirim tercihleri ve ICS feed token tabloları
-- E-posta bildirimleri için r8t_users tablosunda u_email (veya e-posta alanı) olmalıdır.

-- Kullanıcı bildirim tercihleri
CREATE TABLE IF NOT EXISTS r8t_edts_calendar_notification_prefs (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    email_enabled TINYINT(1) NOT NULL DEFAULT 1,
    push_enabled TINYINT(1) NOT NULL DEFAULT 0,
    reminder_minutes INT UNSIGNED NOT NULL DEFAULT 1440,
    quiet_hours_start TIME NULL,
    quiet_hours_end TIME NULL,
    created_at DATETIME NULL,
    updated_at DATETIME NULL,
    UNIQUE KEY uq_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Gönderilen bildirim logu (tekrar gönderimi önlemek için)
CREATE TABLE IF NOT EXISTS r8t_edts_calendar_notification_log (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    d_id INT UNSIGNED NOT NULL,
    channel VARCHAR(20) NOT NULL,
    sent_at DATETIME NOT NULL,
    KEY idx_user_sent (user_id, sent_at),
    KEY idx_d_channel (d_id, channel)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Web Push abonelikleri
CREATE TABLE IF NOT EXISTS r8t_edts_push_subscriptions (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    endpoint VARCHAR(500) NOT NULL,
    p256dh VARCHAR(255) NOT NULL,
    auth VARCHAR(255) NOT NULL,
    user_agent VARCHAR(500) NULL,
    created_at DATETIME NULL,
    UNIQUE KEY uq_endpoint (endpoint(255)),
    KEY idx_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ICS / abonelik feed token (kullanıcıya özel URL)
CREATE TABLE IF NOT EXISTS r8t_edts_calendar_feed_tokens (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    token VARCHAR(64) NOT NULL,
    created_at DATETIME NULL,
    UNIQUE KEY uq_token (token),
    UNIQUE KEY uq_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
