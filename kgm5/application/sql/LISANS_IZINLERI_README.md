# Lisans Yönetimi modülü izinleri

Lisans Yönetimi menüsünü ve sayfalarını görebilmesi için kullanıcı gruplarına ve/veya statülere **lisans** controller yetkisi verilmelidir.

## Yöntem 1: Panel üzerinden (önerilen)

1. **Kullanıcı İşlemleri** > **Grup İşlemleri** bölümüne gidin.
2. Lisans Yönetimi’ne erişmesini istediğiniz grubu seçip **Yetkiler** sayfasını açın.
3. Listede **Lisans Yönetimi** (lisans) satırını bulun; **Görüntüle (read)**, **Listele (list)** ve gerekirse **Yazma / Güncelleme (write, update)** kutucuklarını işaretleyin.
4. Kaydedin.

Statü bazlı yetkilendirme kullanıyorsanız: **Statü İşlemleri** > ilgili statü > Yetkiler sayfasında aynı şekilde **lisans** modülüne yetki verin.

## Yöntem 2: SQL ile (units ile aynı yetki)

`r8t_sys_group_permissions` ve `r8t_sys_statu_permissions` tablolarında birim (units) yetkisi olan gruplara/statülere lisans yetkisi eklemek için `subscription_columns_r8t_sys_unitlist.sql` dosyasının sonundaki yorum satırlarındaki INSERT örneklerini tablo yapınıza uyarlayıp çalıştırabilirsiniz. Tabloda `gp_read`, `gp_list`, `gp_write`, `gp_update`, `gp_delete` (ve statü için `sp_*`) sütunları bulunmalıdır.
