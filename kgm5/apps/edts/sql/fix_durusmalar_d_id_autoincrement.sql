-- =============================================================================
-- r8t_edts_durusmalar tablosunda d_id sütununa AUTO_INCREMENT ekleme
-- Sorun: Yeni kayıtlarda d_id hep 0 olarak kaydediliyor
-- =============================================================================
-- phpMyAdmin veya MySQL client ile çalıştırın.
-- NOT: Tablo adı farklıysa (örn. dbprefix kullanılıyorsa) scripti buna göre düzenleyin.
-- =============================================================================

-- Adım 1: d_id=0 olan kayıtlara benzersiz ID ata
-- (d_id PRIMARY KEY ise en fazla 1 adet d_id=0 olabilir)
SET @next_id = (SELECT COALESCE(MAX(d_id), 0) + 1 FROM r8t_edts_durusmalar);
UPDATE r8t_edts_durusmalar SET d_id = @next_id WHERE d_id = 0;

-- Adım 2: d_id sütununa AUTO_INCREMENT ekle
ALTER TABLE r8t_edts_durusmalar 
MODIFY COLUMN d_id INT(11) NOT NULL AUTO_INCREMENT;

-- Adım 3: Sıradaki ID değerini ayarla
SET @ai_val = (SELECT COALESCE(MAX(d_id), 0) + 1 FROM r8t_edts_durusmalar);
SET @stmt = CONCAT('ALTER TABLE r8t_edts_durusmalar AUTO_INCREMENT = ', @ai_val);
PREPARE s FROM @stmt;
EXECUTE s;
DEALLOCATE PREPARE s;
