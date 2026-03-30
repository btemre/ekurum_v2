-- Kullanıcı bazlı günlük AI kota tablosu
-- Her kullanıcıya ayrı günlük kota atanabilir; kayıt yoksa ai_config.ai_rate_limit_per_user kullanılır.
-- Çalıştırma: Bu dosyayı veritabanında çalıştırın.

CREATE TABLE IF NOT EXISTS `r8t_edts_ai_user_quota` (
  `user_id` INT UNSIGNED NOT NULL COMMENT 'r8t_users.u_id',
  `daily_quota` INT UNSIGNED NOT NULL DEFAULT 100 COMMENT 'Günlük AI istek kotası',
  `updated_at` INT UNSIGNED NULL DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Kullanıcı bazlı günlük AI kota';

-- Örnek: Belirli kullanıcılara farklı kota atamak için:
-- INSERT INTO r8t_edts_ai_user_quota (user_id, daily_quota, updated_at) VALUES (1, 50, UNIX_TIMESTAMP()) ON DUPLICATE KEY UPDATE daily_quota=50, updated_at=UNIX_TIMESTAMP();
-- INSERT INTO r8t_edts_ai_user_quota (user_id, daily_quota, updated_at) VALUES (2, 200, UNIX_TIMESTAMP()) ON DUPLICATE KEY UPDATE daily_quota=200, updated_at=UNIX_TIMESTAMP();
