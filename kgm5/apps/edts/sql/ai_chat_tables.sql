-- YZ Ekürüm AI Asistan: konuşma oturumları ve mesajlar
-- Çalıştırma: Bu dosyayı veritabanında çalıştırın (r8t_ prefix projenize göre ayarlanabilir).

-- Oturumlar: her kullanıcı için sohbet oturumu
CREATE TABLE IF NOT EXISTS `r8t_edts_ai_sessions` (
  `session_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `title` VARCHAR(255) NOT NULL DEFAULT 'Yeni sohbet',
  `created_at` INT UNSIGNED NOT NULL,
  `updated_at` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`session_id`),
  KEY `idx_user_updated` (`user_id`, `updated_at` DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='YZ Ekürüm AI sohbet oturumları';

-- Mesajlar: oturumdaki her mesaj (user veya model)
CREATE TABLE IF NOT EXISTS `r8t_edts_ai_messages` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `session_id` INT UNSIGNED NOT NULL,
  `role` ENUM('user','model') NOT NULL,
  `content` TEXT NOT NULL,
  `token_estimate` INT UNSIGNED NULL DEFAULT NULL,
  `created_at` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_session_created` (`session_id`, `created_at`),
  CONSTRAINT `fk_ai_messages_session` FOREIGN KEY (`session_id`) REFERENCES `r8t_edts_ai_sessions` (`session_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='YZ Ekürüm AI sohbet mesajları';
