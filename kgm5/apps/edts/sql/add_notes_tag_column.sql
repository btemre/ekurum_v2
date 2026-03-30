-- r8t_edts_notes tablosuna n_tag ve n_reminder_dismissed_at sütunları ekleme
-- Etiketler: unutma, acil, onemli, dikkat_et, duzeltilecek, sor
-- n_reminder_dismissed_at: Tekrar Hatırlatma tıklandığında set edilir, modal bir daha açılmaz

ALTER TABLE `r8t_edts_notes`
ADD COLUMN `n_tag` VARCHAR(50) NULL DEFAULT NULL COMMENT 'Etiket: unutma, acil, onemli, dikkat_et, duzeltilecek, sor' AFTER `n_content`,
ADD COLUMN `n_reminder_dismissed_at` DATETIME NULL DEFAULT NULL COMMENT 'Tekrar Hatırlatma tıklandığında; Kapat tıklandığında NULL kalır' AFTER `n_reminder_at`;
