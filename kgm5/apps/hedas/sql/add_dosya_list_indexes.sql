-- =============================================================================
-- HEDAS Dosya listesi performans indeksleri
-- Amaç: Dosyalar listeleme sayfasında COUNT ve SELECT sorgularını hızlandırmak
-- =============================================================================
-- phpMyAdmin veya MySQL client ile çalıştırın.
-- NOT: Tablo adı farklıysa (örn. dbprefix kullanılıyorsa) scripti buna göre düzenleyin.
-- =============================================================================

-- Mevcut indeksleri kontrol etmek için: SHOW INDEX FROM r8t_edys_dosya;
-- NOT: Bu scripti sadece bir kez çalıştırın. İndeks zaten varsa hata alırsınız.

-- 1. d_status + d_adddate bileşik indeksi (tarih filtreli listelerde - varsayılan filtre)
-- WHERE d_status=1 AND (d_adddate >= X AND d_adddate <= Y)
CREATE INDEX idx_dosya_status_adddate 
ON r8t_edys_dosya (d_status, d_adddate);

-- 2. d_kurumdosyano indeksi (kurum dosya no ile arama)
CREATE INDEX idx_dosya_kurumdosyano 
ON r8t_edys_dosya (d_kurumdosyano);

-- 3. r8t_edys_dosya_mahkemeler tablosu - dm_dosyaid (JOIN için)
CREATE INDEX idx_dosya_mahkemeler_dosyaid 
ON r8t_edys_dosya_mahkemeler (dm_dosyaid);
