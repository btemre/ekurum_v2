# HEDAS ve EDTS – Mevcut Özellikler, Sayfalar ve Veri Yapısı

Bu doküman, eKurum KGM5 projesindeki **HEDAS** (Hukuki Evrak ve Dava Takip Sistemi) ile **EDTS** (Duruşma Takip Sistemi) uygulamalarının mevcut özelliklerini, sayfalarını, tutulan verileri ve çalışma yapısını özetler. Kod tabanı (controller, model, view, veritabanı referansları) incelenerek hazırlanmıştır.

---

## 1. Genel Mimari

- **Çatı:** CodeIgniter 3, çoklu uygulama (multi-app) yapısı.
- **Konum:** `kgm5/apps/hedas` (HEDAS), `kgm5/apps/edts` (EDTS).
- **Yetkilendirme:** Uygulama bazlı (`isAllowedViewApp("hedas")` / `isAllowedViewApp("edts")`) ve modül bazlı (list/view/write/delete) kontroller; kullanıcı girişi zorunlu.
- **Ortak yapı:** Controller → Model (RT_Model tabanlı) → View; listeler çoğunlukla DataTables ile AJAX API (`api_list`, filtre, sıralama).

---

## 2. HEDAS (Hukuki Evrak ve Dava Takip Sistemi)

### 2.1 Amaç

Dava dosyaları, gelen/giden evraklar ve ceza-iptal başvurularının kaydı, listelenmesi, aranması ve raporlanması; not ve hatırlatma yönetimi.

### 2.2 Sayfalar ve URL’ler

| Sayfa / İşlem | URL / Erişim | Açıklama |
|----------------|--------------|----------|
| **Anasayfa (Dashboard)** | `dashboard` | Özet istatistikler: dosya toplamları (KGM davacı/davalı, diğer), gelen/giden/genel evrak sayıları, ceza-iptal durumları; son 5 dosya, son 5 gelen/giden, son 5 ceza-iptal kaydı. |
| **Dosya İşlemleri** | | |
| → Dosyalar | `dosya` | Dosya listesi (DataTables); kurum dosya no, davacı, davalı, dava konusu, mahkeme, esas no, karar no, mevki/plaka, etiketler vb. |
| → Yeni Kayıt | Modal `#kt_modal_new_dosya` | Yeni dosya evrakı ekleme (tek form). |
| → Çöp Kutusu | `dosya/archive` | Silinen (soft delete) dosyalar listesi. |
| → İstatistikler | `dosya/istatistik` | Davacı, davalı, mahkeme, dava konu açıklaması bazlı grafik/liste. |
| → Dosya detay/önizleme | `dosya_v/preview` | Dosya detayı görüntüleme (view dosyası mevcut). |
| **Gelen/Giden Evraklar** | | |
| → Evrak Listesi | `gelengiden` | Gelen/giden/genel evrak listesi; tür, kaynak, açıklama, sayı, dosya no, kategori, tarih. |
| → Arama ve Dışarı Aktarma | `gelengiden/ara` | Tarih aralığı ve filtreyle arama, dışa aktarma. |
| → Yeni Kayıt | Modal `#kt_modal_new_ggevrak` | Yeni gelen/giden evrak kaydı. |
| → Çöp Kutusu | `gelengiden/archive` | Silinen evraklar. |
| **Ceza-İptal Başvuruları** | | |
| → Başvuru Listesi | `cezaiptal` | Ceza-iptal başvuruları listesi; kurum dosya no, itiraz eden, dava konusu, mahkeme, esas no, karar tarihi, plaka, ceza seri no, evrak durumu (yetkisizlik, kabul, red, kısmi kabul/red vb.). |
| → Arama ve Dışarı Aktarma | `cezaiptal/ara` | Filtre ve dışa aktarma. |
| → Yeni Kayıt | Modal `#kt_modal_new_cezaiptal` | Yeni ceza-iptal başvurusu. |
| → Çöp Kutusu | `cezaiptal/archive` | Silinen başvurular. |
| **Ceza-İptal Aralıklı** | `cezaiptal-aralikli` (controller) | Aralıklı ceza-iptal başvuruları için ayrı listeleme/arama. |
| **Not ve Hatırlatmalar** | `Notesreminders` controller | Kullanıcıya özel not ve hatırlatma listesi (`r8t_edys_notes_reminders`); kaydetme API’si (`save_api`). Menüde ayrı bir “Not/Hatırlatma” linki olmayabilir; dashboard veya başka bir yerden erişilebilir. |

### 2.3 Tutulan Veriler (HEDAS)

#### Ana tablolar

- **r8t_edys_dosya** – Dava dosyası evrakı  
  Örnek alanlar: `d_id`, `d_status`, `d_kurumdosyano`, `d_davaci`, `d_davali`, `d_davakonusu`, `d_davakonuaciklama`, `d_mevkiplaka`, `d_tags`, `d_onamailami`, `d_bozmailami`, `d_istinafkabul`, `d_istinafred`, `d_kararkesinlestirme`, `d_mirascilik`, `d_adddate`, `d_adduser` vb.

- **r8t_edys_dosya_mahkemeler** – Dosyaya bağlı mahkeme kayıtları (1-N)  
  Örnek alanlar: `dm_id`, `dm_dosyaid`, `dm_mahkeme`, `dm_acilistarihi`, `dm_esasno`, `dm_kararno`, `dm_karartarihi`, `dm_aciklama`, `dm_adddate`, `dm_adduser`.

- **r8t_edys_ggevrak** – Gelen/giden evrak  
  Örnek alanlar: `gg_id`, `gg_status`, `gg_tur` (0: gelen, 1: giden, 2: genel), `gg_kaynak`, `gg_aciklama`, `gg_sayi`, `gg_dosyano`, `gg_kategori` (0: genel, 1: personel, 2: tamim genelge), `gg_tarih`, `gg_adddate` vb.

- **r8t_edys_cezaiptal** – Ceza-iptal başvurusu  
  Örnek alanlar: `ci_id`, `ci_status`, `ci_kurumdosyano`, `ci_itirazeden`, `ci_davakonu`, `ci_cezakonu`, `ci_mahkeme`, `ci_esasno`, `ci_karartarih`, `ci_plaka`, `ci_cezaserino`, `ci_evrakdurum` (0: yetkisizlik, 1: kabul, 2: red, 3: kısmi kabul, 4: kısmi red, 5: kısmi kabul/red, 6: birleştirilmiş, 7: belirlenmemiş), `ci_acilistarih` vb.

- **r8t_edys_tags** – Etiket tanımları (isim, renk); dosya ve evrak kayıtlarında kullanılır.

- **r8t_edys_notes_reminders** – Kullanıcı not ve hatırlatmaları; `nr_userid` ile kullanıcıya bağlı.

### 2.4 Çalışma Yapısı (HEDAS)

- **Listeler:** DataTables; sunucu taraflı sayfalama, sıralama ve filtreleme; controller’da `api_list` benzeri metodlar, tarih aralığı ve alan bazlı filtre (LIKE, eşitlik).
- **Ekleme/Güncelleme:** Çoğunlukla modal + AJAX; form verisi JSON veya POST ile gönderilir; `api_newrecord`, `api_update` vb.
- **Silme:** Soft delete (`d_status` / `gg_status` / `ci_status` = -1 veya 0); çöp kutusu sayfalarında listelenir.
- **Dosya–Mahkeme ilişkisi:** Bir dosyada birden fazla mahkeme kaydı (r8t_edys_dosya_mahkemeler); listeleme ve arama JOIN ile yapılır.
- **Yetki:** Her sayfa öncesi `isAllowedViewApp("hedas")` ve modül bazlı `isDbAllowedListModule()`, `isDbAllowedWriteModule()`, `isDbAllowedDeleteModule()` kontrolleri.

---

## 3. EDTS (Duruşma Takip Sistemi)

### 3.1 Amaç

Duruşma kayıtlarının takibi: esas no, mahkeme, dosya no, duruşma tarihi, avukat, memur, taraf, işlem türü, tutanak bilgisi; listeleme, arama, “Duruşmalarım”, Excel’den toplu aktarım; AI asistan ile doğal dil sorgulama ve özet.

### 3.2 Sayfalar ve URL’ler

| Sayfa / İşlem | URL / Erişim | Açıklama |
|----------------|--------------|----------|
| **Anasayfa (Dashboard)** | `dashboard` | Avukat ve memur bazlı duruşma sayıları (yıllık), haftalık duruşma listesi. |
| **AI Asistan** | `ai_assistant` | Tam sayfa AI asistan; doğal dil soru, Text-to-SQL, özet kartları. API: `ai_assistant/api_ask`, `api_summary`, `api_text_to_sql`. |
| **Duruşma İşlemleri** | | |
| → Tüm Duruşmalar | `durusmalar` | Duruşma listesi (DataTables): esas no, mahkeme, dosya no, duruşma tarihi, avukat, memur, dosya türü, taraf, işlem, taraf bilgisi, takip, tutanak, etiketler, eklenme tarihi. |
| → Arama ve Dışarı Aktarma | `durusmalar/ara` | Tarih aralığı (ekleme + duruşma), memur/avukat, işlem, mahkeme vb. filtre; dışa aktarma. |
| → Duruşmalarım | `durusmalar/durusmalarim` | Giriş yapan kullanıcının atandığı duruşmalar (memur veya avukat ID’ye göre). |
| → Excel’den Aktar | `importexcel` | Excel dosyası yükleyerek toplu duruşma kaydı; `r8t_edts_importexcel` ile geçici/aktarım verisi. |
| → Yeni Duruşma Ekle | Modal `#kt_modal_new_durusmalar_manuel` | Tekil manuel duruşma ekleme. |
| → Çöp Kutusu | `durusmalar/archive` | Silinen (soft delete) duruşmalar. |
| → Hareketler | `durusmalar/hareketler` | Duruşma hareketleri (log/geçmiş) listesi. |
| → İstatistikler | `durusmalar/istatistik` | Avukat, memur, taraf, mahkeme, işlem bazlı dağılım; haftalık/aylık listeler; karar sayıları. |

### 3.3 Tutulan Veriler (EDTS)

#### Ana tablolar

- **r8t_edts_durusmalar** – Duruşma kaydı  
  Örnek alanlar: `d_id`, `d_status` (1: aktif), `d_esasno`, `d_mahkeme`, `d_dosyano`, `d_dosyaturu`, `d_durusmatarihi` (Unix timestamp), `d_avukat`, `d_avukatid`, `d_memur`, `d_memurid`, `d_taraf`, `d_tarafbilgisi`, `d_islem`, `d_takip`, `d_tutanak`, `d_tags`, `d_adddate` (Unix timestamp).

- **r8t_edts_importexcel** – Excel aktarımı için kullanılan tablo (Importexcel_model).

- **r8t_users** – Kullanıcılar (avukat/memur eşlemesi için AI ve filtrelerde kullanılır).

### 3.4 Çalışma Yapısı (EDTS)

- **Listeler:** DataTables; `durusmalar/api_list` benzeri API’ler; ekleme tarihi ve duruşma tarihi aralığı, memur ID, avukat ID, işlem, mahkeme vb. filtreler.
- **Duruşmalarım:** Aynı listeleme API’si; `d_memurid` veya `d_avukatid` = giriş yapan kullanıcı ID’si ile filtrelenir.
- **AI Asistan:** Gemini API entegrasyonu; Text-to-SQL (sadece `r8t_edts_durusmalar` ve `r8t_users`), özet API (avukat/memur bazlı, haftalık sayı), serbest soru-cevap.
- **Excel aktarımı:** Importexcel sayfasından dosya yükleme; Excel’den satır okunup duruşma kayıtları oluşturulur.
- **Routes:** `config/routes.php` içinde `ai_assistant`, `ai_assistant/api_ask`, `api_summary`, `api_text_to_sql` tanımlı; diğer controller/method’lar varsayılan CodeIgniter segment yapısıyla çalışır.
- **Yetki:** `isAllowedViewApp("edts")`, modül bazlı list/view/write/delete; AI asistan EDTS yetkisi ile açılır.

---

## 4. Özet Tablo

| Özellik | HEDAS | EDTS |
|---------|--------|------|
| **Odak** | Dava dosyası, gelen/giden evrak, ceza-iptal | Duruşma takibi |
| **Ana listeler** | Dosya, Gelen/Giden, Ceza-İptal | Duruşmalar (tümü, arama, duruşmalarım) |
| **Dashboard** | Dosya/evrak/ceza-iptal özeti, son kayıtlar | Avukat/memur bazlı, haftalık duruşma |
| **Arama / dışa aktarma** | Dosya, Gelengiden, Cezaiptal için ayrı “Ara” sayfaları | Duruşmalar/ara |
| **Çöp kutusu** | Dosya, Gelengiden, Cezaiptal archive | Duruşmalar archive |
| **İstatistik** | Dosya (davacı, davalı, mahkeme, dava konusu) | Duruşmalar (avukat, memur, taraf, mahkeme, işlem; haftalık/aylık) |
| **Toplu veri** | — | Excel’den aktar (Importexcel) |
| **Özel modül** | Not ve hatırlatma (Notesreminders) | AI Asistan (Text-to-SQL, özet, soru-cevap) |
| **İlişkisel veri** | Dosya ↔ Mahkemeler (1-N) | Duruşma tek tablo; kullanıcı ile avukat/memur ID |

---

## 5. Teknik Referanslar

- **HEDAS controller’lar:** `Dosya`, `Gelengiden`, `Cezaiptal`, `Cezaiptal-aralikli`, `Dashboard`, `Notesreminders`.
- **EDTS controller’lar:** `Durusmalar`, `Dashboard`, `Ai_assistant`, `Importexcel`, `Auth`.
- **Ortak model tabanı:** `RT_Model` (get, get_all, add, update, ek_query, ek_get_all vb.).
- **Veritabanı indeks önerileri:** `kgm5/database_indexes.sql` (r8t_edys_dosya, r8t_edys_dosya_mahkemeler, r8t_edts_durusmalar).

Bu doküman, yeni özellik planlama (takvim, mobil, evrak tarama, AI genişletmeleri) ve mevcut sayfa/veri yapısının referansı olarak kullanılabilir.
