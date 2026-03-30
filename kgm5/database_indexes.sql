-- Performance index recommendations for eKurum KGM5
-- Run this SQL manually after verifying table/column names match your schema
-- Use: mysql -u user -p database < database_indexes.sql
-- Note: Omit indexes that already exist (MySQL will error on duplicate)

-- HEDAS: r8t_edys_dosya
CREATE INDEX idx_edys_dosya_status ON r8t_edys_dosya(d_status);
CREATE INDEX idx_edys_dosya_davaci ON r8t_edys_dosya(d_davaci(50));
CREATE INDEX idx_edys_dosya_davali ON r8t_edys_dosya(d_davali(50));

-- HEDAS: r8t_edys_dosya_mahkemeler
CREATE INDEX idx_edys_dm_dosyaid ON r8t_edys_dosya_mahkemeler(dm_dosyaid);

-- EDTS: r8t_edts_durusmalar
CREATE INDEX idx_edts_durusmalar_status ON r8t_edts_durusmalar(d_status);
CREATE INDEX idx_edts_durusmalar_tarih ON r8t_edts_durusmalar(d_durusmatarihi);
CREATE INDEX idx_edts_durusmalar_adddate ON r8t_edts_durusmalar(d_adddate);
CREATE INDEX idx_edts_durusmalar_memur ON r8t_edts_durusmalar(d_memurid);
CREATE INDEX idx_edts_durusmalar_avukat ON r8t_edts_durusmalar(d_avukatid);
