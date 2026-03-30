-- =============================================================================
-- r8t_edts_durusmalar tablosu listeleme performans indeksleri
-- Amaç: Listeleme sayfalarında COUNT ve SELECT sorgularını hızlandırmak
-- =============================================================================
-- phpMyAdmin veya MySQL client ile çalıştırın.
-- NOT: Tablo adı farklıysa (örn. dbprefix kullanılıyorsa) scripti buna göre düzenleyin.
-- =============================================================================

-- Mevcut indeksleri kontrol etmek için: SHOW INDEX FROM r8t_edts_durusmalar;
-- NOT: Bu scripti sadece bir kez çalıştırın. İndeks zaten varsa hata alırsınız.
-- Eğer daha önce çalıştırdıysanız, sadece yeni eklenen indeksleri çalıştırın.

-- 1. d_status + d_adddate bileşik indeksi (tarih filtreli listelerde kullanılır)
-- filter modunda: WHERE d_status=1 AND (d_adddate >= X AND d_adddate <= Y)
CREATE INDEX idx_durusmalar_status_adddate 
ON r8t_edts_durusmalar (d_status, d_adddate);

-- 2. d_status + d_durusmatarihi bileşik indeksi (duruşma tarihi filtrelerinde)
CREATE INDEX idx_durusmalar_status_durusmatarihi 
ON r8t_edts_durusmalar (d_status, d_durusmatarihi);

-- 3. d_memurid indeksi (memur filtresinde: d_memurid IN (...))
CREATE INDEX idx_durusmalar_memurid 
ON r8t_edts_durusmalar (d_memurid);

-- 4. d_avukatid indeksi (avukat filtresinde)
CREATE INDEX idx_durusmalar_avukatid 
ON r8t_edts_durusmalar (d_avukatid);

-- 5. d_adddate sıralama için (ORDER BY d_adddate DESC)
CREATE INDEX idx_durusmalar_adddate 
ON r8t_edts_durusmalar (d_adddate DESC);

-- 6. Dashboard bugünkü duruşmalar: d_status + d_durusmatarihi (ana sorgu)
-- api_listDashboard: WHERE d_status=1 AND d_durusmatarihi >= X AND d_durusmatarihi <= Y
-- Bu zaten 2 numaralı index ile karşılanıyor, ek gerekmez.

-- 7. d_status + d_memurid bileşik indeksi (Duruşmalarım: WHERE d_status=1 AND d_memurid=X)
CREATE INDEX idx_durusmalar_status_memurid 
ON r8t_edts_durusmalar (d_status, d_memurid);

-- 8. FULLTEXT indeks - metin araması (dText) için
-- api_list ve api_mylist: LIKE '%keyword%' ile 13 sütunda arama yapar
-- FULLTEXT index bu aramaları ciddi oranda hızlandırır
ALTER TABLE r8t_edts_durusmalar 
ADD FULLTEXT INDEX idx_durusmalar_fulltext 
(d_dosyaturu, d_mahkeme, d_esasno, d_tarafbilgisi, d_memur, d_avukat, d_islem, d_dosyano, d_taraf, d_aciklama, d_ilgiliavukatlar, d_ilgilimemurlar, d_tags);

-- 9. d_durusmatarihi sıralama için (ORDER BY d_durusmatarihi ASC - dashboard)
CREATE INDEX idx_durusmalar_durusmatarihi 
ON r8t_edts_durusmalar (d_durusmatarihi);

-- =============================================================================
-- 10. ANALYZE TABLE - Index istatistiklerini güncelle
-- MySQL optimizer'ın yeni index'leri doğru kullanması için ZORUNLU
-- Index'leri oluşturduktan sonra BU KOMUTU MUTLAKA ÇALIŞTIRIN
-- =============================================================================
ANALYZE TABLE r8t_edts_durusmalar;
ANALYZE TABLE r8t_sys_userlogs;
ANALYZE TABLE r8t_sys_mahkemeler;
