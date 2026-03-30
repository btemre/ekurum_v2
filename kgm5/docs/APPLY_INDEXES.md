# Veritabanı İndekslerini Uygulama

`database_indexes.sql` dosyasındaki performans indeksleri iki şekilde uygulanabilir:

## 1. PHP CLI script ile (önerilen)

```bash
cd kgm5
php scripts/apply_database_indexes.php
```

Script `.env` dosyasındaki DB bilgilerini kullanır. Yoksa varsayılan değerleri kullanır.

## 2. MySQL komut satırı ile

```bash
mysql -u KULLANICI -p VERITABANI < database_indexes.sql
```

Örnek:
```bash
mysql -u auluslar_5bolge_ekurum -p auluslar_5bolge_ekurum < database_indexes.sql
```

**Not:** İndeks zaten varsa MySQL hata verecektir. Bu durumda o satırı atlayın veya `DROP INDEX` ile önce silin.

## İndeks listesi

| Tablo | İndeks |
|-------|--------|
| r8t_edys_dosya | d_status, d_davaci, d_davali |
| r8t_edys_dosya_mahkemeler | dm_dosyaid |
| r8t_edts_durusmalar | d_status, d_durusmatarihi, d_adddate, d_memurid, d_avukatid |
