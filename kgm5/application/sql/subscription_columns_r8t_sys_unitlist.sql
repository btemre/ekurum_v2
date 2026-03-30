-- =============================================================================
-- Abonelik / Lisans süre takibi (SAAS planı 5.2.1)
-- =============================================================================
-- 1) r8t_sys_unitlist tablosuna abonelik sütunları eklenir.
-- 2) Lisans Yönetimi menüsü için izinler: Grup/Statü yetkilerinden "lisans"
--    modülüne read/list/write/update verin (Kullanıcı İşlemleri > Grup İşlemleri
--    > ilgili grubun Yetkiler sayfasında "Lisans Yönetimi" satırını işaretleyin).
--    Alternatif: Aşağıdaki 2. blokla units ile aynı yetkiye sahip gruplara
--    lisans yetkisi eklenir (tek seferlik).
-- Çalıştırmadan önce yedek alın.
-- =============================================================================

-- 1) Abonelik sütunları

ALTER TABLE r8t_sys_unitlist
  ADD COLUMN subscription_start_date DATE NULL DEFAULT NULL COMMENT 'Lisans/abonelik başlangıç tarihi',
  ADD COLUMN subscription_end_date DATE NULL DEFAULT NULL COMMENT 'Lisans/abonelik bitiş tarihi',
  ADD COLUMN subscription_period VARCHAR(20) NULL DEFAULT 'yearly' COMMENT 'monthly / yearly',
  ADD COLUMN subscription_status VARCHAR(20) NULL DEFAULT 'active' COMMENT 'active / expired / grace / suspended / cancelled',
  ADD COLUMN is_demo TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Demo birim: 0=hayır, 1=evet',
  ADD COLUMN demo_end_date DATE NULL DEFAULT NULL COMMENT 'Demo bitiş tarihi (is_demo=1 ise)';

-- İsteğe bağlı: Mevcut birimleri aktif ve 1 yıl süreli yapmak için:
UPDATE r8t_sys_unitlist SET subscription_status = 'active', subscription_period = 'yearly', subscription_start_date = CURDATE(), subscription_end_date = DATE_ADD(CURDATE(), INTERVAL 1 YEAR) WHERE subscription_status IS NULL OR subscription_status = '';

-- =============================================================================
-- 2) Lisans Yönetimi izinleri (units ile aynı yetkiye sahip gruplara lisans ekle)
-- Tabloda gp_read, gp_list, gp_write, gp_update, gp_delete sütunları varsa çalıştırın.
-- =============================================================================
-- Grup izinleri: units yetkisi olan her gruba lisans yetkisi ekle (kayıt yoksa)
-- INSERT INTO r8t_sys_group_permissions (gp_app, gp_groupid, gp_controller, gp_read, gp_list, gp_write, gp_update, gp_delete)
-- SELECT 'sys', gp_groupid, 'lisans', gp_read, gp_list, gp_write, gp_update, gp_delete
-- FROM r8t_sys_group_permissions
-- WHERE gp_app = 'sys' AND gp_controller = 'units'
-- ON DUPLICATE KEY UPDATE gp_read = VALUES(gp_read), gp_list = VALUES(gp_list), gp_write = VALUES(gp_write), gp_update = VALUES(gp_update), gp_delete = VALUES(gp_delete);
--
-- Statü izinleri: units yetkisi olan her statüye lisans yetkisi ekle (kayıt yoksa)
-- INSERT INTO r8t_sys_statu_permissions (sp_app, sp_statuid, sp_controller, sp_read, sp_list, sp_write, sp_update, sp_delete)
-- SELECT 'sys', sp_statuid, 'lisans', sp_read, sp_list, sp_write, sp_update, sp_delete
-- FROM r8t_sys_statu_permissions
-- WHERE sp_app = 'sys' AND sp_controller = 'units';
