# eKurum KGM5 – SaaS Odaklı Kurumsal Geliştirme Planı

Bu doküman, mevcut eKurum (KGM5 – HEDAS, EDTS) projesinin kapsamlı ve SaaS odaklı kurumsal bir platforma evrilmesi için önerilen strateji, mimari ve yol haritasını içerir. CEKAS uygulaması kapsam dışındadır. “Ben olsaydım nasıl yapardım?” sorusuna cevap niteliğindedir.

---

## 1. Mevcut Durum Özeti

| Bileşen | Açıklama |
|--------|----------|
| **Platform** | eKurum – Kurum Genel Müdürlüğü kurumsal portal |
| **Framework** | CodeIgniter 3.x |
| **Uygulamalar** | Ana uygulama + HEDAS (Döküman Yönetim), EDTS (Duruşma Takip) |
| **Veritabanı** | MySQL/MariaDB, uygulama bazlı tablolar |
| **Yapı** | `kgm5/application` + `kgm5/apps/{hedas, edts}` – her app kendi index.php ve application klasörü |

**Kapsam:** Bu plan yalnızca HEDAS ve EDTS uygulamalarını kapsar; CEKAS kapsam dışındadır.

**Güçlü yönler:** Modüler app yapısı, yetki/rol mantığı, mevcut iş akışları.  
**Zayıf/geliştirilebilir yönler:** Multi-tenant yok, API tek tip değil, ölçeklenebilirlik ve SaaS özellikleri (faturalama, self-service, çoklu kurum) sınırlı.

---

## 2. Hedef: SaaS Odaklı Kurumsal Platform

### 2.1 Vizyon

- **SaaS:** Tek kod tabanı, çoklu müşteri (kurum/şube), yazılım lisansı ve kurum bazında abonelik; self-service onboarding.
- **Ödeme modeli:** Online ödeme yok; **kurumsal fatura** ile **kurum bazında** ödeme alınır (havale/EFT, fatura karşılığı).
- **Demo:** Uygulamalar **3 aylık demo (deneme)** olarak sunulabilir; süre sonunda lisanslı aboneliğe geçiş veya erişim kısıtlaması.
- **Kurumsal:** Güvenlik, denetim, SLA, uyumluluk (KVKK, ISO, sektör standartları), kurumsal destek.

### 2.2 Temel Prensipler

1. **API-First:** Tüm iş mantığı API üzerinden erişilebilir; web ve mobil aynı API’yi kullanır.
2. **Multi-Tenant:** Veri ve konfigürasyon kiracı (kurum/şube) bazında ayrılır.
3. **Güvenlik by default:** Kimlik doğrulama, yetkilendirme, audit log, şifreleme merkezi tasarlanır.
4. **Ölçeklenebilirlik:** Stateless API, cache, queue ve veritabanı optimizasyonu ile büyümeye hazırlık.
5. **Gözlemlenebilirlik:** Loglama, metrik, hata izleme ve basit operasyon paneli.

---

## 3. Mimari Önerisi

### 3.1 Genel Mimari (Hedef)

```
┌─────────────────────────────────────────────────────────────────────────┐
│                           KULLANICI KATMANI                              │
├──────────────┬──────────────┬──────────────┬──────────────┬──────────────┤
│  Web (Mevcut │  Mobil App   │  Entegrasyon │  Yönetim     │  Raporlama   │
│  CI3)        │  (Flutter/   │  (API)       │  Paneli      │  / BI        │
│              │  RN)         │              │  (SaaS)      │              │
└──────┬───────┴──────┬───────┴──────┬───────┴──────┬───────┴──────┬───────┘
       │              │              │              │              │
       └──────────────┴──────────────┼──────────────┴──────────────┘
                                     │
┌────────────────────────────────────┼────────────────────────────────────┐
│                    API GATEWAY / BFF (opsiyonel)                         │
│                    Auth, rate limit, routing                             │
└────────────────────────────────────┼────────────────────────────────────┘
                                     │
┌────────────────────────────────────┼────────────────────────────────────┐
│                         UYGULAMA KATMANI                                 │
├────────────────────────────────────┼────────────────────────────────────┤
│  Auth Service   │  HEDAS API  │  EDTS API  │  Tenant / Config API   │
│  (JWT, SSO)     │  (Dosya,    │  (Duruşma) │                        │
│                 │  Cezaiptal) │            │                        │
└─────────────────┴─────────────┴────────────┴────────────────────────┘
                                     │
┌────────────────────────────────────┼────────────────────────────────────┐
│  Cache (Redis)  │  Queue (opsiyonel) │  AI (Gemini vb.)  │  Dosya (S3/NAS)│
└────────────────────────────────────┼────────────────────────────────────┘
                                     │
┌────────────────────────────────────┼────────────────────────────────────┐
│  Veritabanı (MySQL) – Tenant-aware schema veya ayrı DB per tenant        │
└─────────────────────────────────────────────────────────────────────────┘
```

- **Kısa vadede:** Mevcut CodeIgniter uygulamaları korunur; API katmanı aynı codebase içinde tutulur, auth token’a geçilir.
- **Orta vadede:** Kritik servisler (auth, tenant, fatura/lisans takibi) ayrı modüller veya microservice’e taşınabilir.

### 3.2 Multi-Tenant Modeli

**Seçenek A – Shared DB, tenant_id ile ayrım (önerilen başlangıç)**  
- Tüm tablolarda `tenant_id` (veya `kurum_id`) alanı.  
- Her sorgu tenant’a göre filtrelenir.  
- Ucuz, bakımı kolay; veri büyüdükçe indeks ve partisyon ile optimize edilir.

**Seçenek B – Schema per tenant**  
- Aynı MySQL instance’da tenant başına ayrı schema.  
- İzolasyon iyi, yedekleme/taşıma esnek; schema güncellemeleri her tenant’a uygulanmalı.

**Seçenek C – DB per tenant**  
- Kritik müşteriler veya yüksek izolasyon ihtiyacı için.  
- Maliyet ve operasyon yükü artar.

**Öneri:** Başlangıçta Seçenek A; tenant sayısı ve regülasyon ihtiyacına göre B/C’ye geçiş planlanır.

### 3.3 Kimlik Doğrulama ve Yetkilendirme

| Konu | Öneri |
|------|--------|
| **Web** | Mevcut session’a ek olarak veya yerine JWT (access + refresh token). |
| **Mobil / API** | Sadece JWT; token süresi kısa, refresh token ile yenileme. |
| **SSO** | Kurumsal müşteriler için SAML2 veya OIDC (opsiyonel faz). |
| **Yetki** | Rol tabanlı (RBAC); roller tenant’a özel tanımlanabilir. Mevcut `isAllowedViewApp`, modül yetkileri API’de de merkezi kontrol edilir. |
| **Audit** | Tüm kritik işlemler (giriş, veri değişikliği, yetki değişikliği) loglanır; tenant_id, user_id, ip, timestamp. |

---

## 4. Teknoloji ve Katmanlar

### 4.1 Backend (Mevcut + Evrim)

- **Kısa vade:** CodeIgniter 3 kalır; API endpoint’leri standartlaştırılır (REST, JSON, aynı auth).
- **Orta vade:**  
  - Yeni modüller veya “v2 API” için PHP 8+ ve Laravel/Symfony gibi framework değerlendirilebilir.  
  - Veya CI4’e geçiş + modüler yapı (her app bir modül).
- **Veritabanı:** MySQL/MariaDB; connection pooling, read replica (ileride), indeks ve sorgu optimizasyonu.

### 4.2 API Tasarımı

- **Format:** JSON.  
- **Versiyonlama:** URL’de `/v1/` veya header’da `Accept: application/vnd.ekurum.v1+json`.  
- **Hata formatı:** Tek tip; `code`, `message`, `details` (opsiyonel).  
- **Sayfalama:** `limit` + `offset` veya `cursor`; response’da `total`, `next_cursor` vb.  
- **Filtreleme/sıralama:** Query parametreleri veya güvenli bir filter DSL (örn. `?filter[status]=1&sort=-created_at`).

### 4.3 Frontend (Web)

- Mevcut CodeIgniter view’lar kademeli olarak korunabilir.  
- Yeni sayfalar veya “modern” modüller için SPA (Vue/React) sadece belirli sayfalarda kullanılabilir (hybrid).  
- Tema ve erişilebilirlik: Kurumsal görünüm, tutarlı bileşen kütüphanesi.

### 4.4 Mobil

- Tek uygulama (Flutter veya React Native), HEDAS ve EDTS modları.  
- API-first sayesinde tüm veri API’den; push bildirim, offline cache (opsiyonel) sonraki fazda.

### 4.5 AI ve Analitik

- Mevcut RAG + Text-to-SQL korunur.  
- İleride: rapor özetleme, anomali uyarıları, tahminleme (örn. duruşma yoğunluğu) için ayrı pipeline’lar eklenebilir.

---

## 5. SaaS Özellikleri

### 5.1 Kiracı (Tenant) Yönetimi

- **Onboarding:** Yeni kurum ekleme; varsayılan rol/izinler, limitler (kullanıcı sayısı, depolama).  
- **Konfigürasyon:** Tenant bazlı ayarlar (dil, tarih formatı, logo, tema, modül aç/kapa).  
- **İzolasyon:** Veri ve konfigürasyonun tenant_id ile kesin ayrımı; test ve production ortamları.

### 5.2 Abonelik, Lisanslama ve Ödeme Modeli

- **Ödeme:** Online ödeme (kredi kartı, vb.) **yoktur**. Ödeme **kurumsal fatura** ile alınır; kurum bazında havale/EFT veya fatura karşılığı ödeme.
- **Planlar:** Örn. Temel / Profesyonel / Kurumsal; özellik seti ve limitler farklı. Her kurum (tenant) bir plana bağlanır.
- **Limitler:** Kullanıcı sayısı, depolama; plana göre tanımlanır.
- **Faturalama:** Kurum bazında fatura kesimi (dönemsel veya proforma); ödeme takibi (tahsilat durumu) sistemde tutulur. Abonelik durumu (aktif/dondurulmuş/iptal) fatura ve tahsilata göre API ve girişte kontrol edilir.

### 5.2.1 Genel Kullanım – Lisans / Abonelik Süre Takibi

Demo dışında **ücretli (genel) kullanım** için de lisans/abonelik süresinin takip edilmesi önerilir. Böylece her kurum için geçerli kullanım dönemi net olur, yenileme ve süre bitimi yönetilebilir.

#### Amaç

- Ücretli aboneliklerde **lisans süresi** (başlangıç–bitiş) kayıt altında olsun.
- **Yenileme** öncesi hatırlatma ve süre bitiminde erişim/uyarı davranışı tanımlansın.
- Fatura dönemi ile lisans dönemi ilişkilendirilsin; tahsilat gecikse bile süre bitimine göre kilit/grace period uygulanabilsin.

#### Veri modeli önerisi

- **Tenant (kurum) veya abonelik kaydında:**
  - `subscription_start_date` – lisans/abonelik başlangıç tarihi
  - `subscription_end_date` – lisans/abonelik bitiş tarihi
  - `subscription_period` – dönem tipi: `monthly` / `yearly` (yenileme hesaplaması için)
  - `subscription_status` – `active` / `expired` / `grace` (ödemede gecikme, kısa ek süre) / `suspended` / `cancelled`
- **İsteğe bağlı:** Fatura kayıtları (fatura_no, tarih, vade, tahsilat_tarihi) ile abonelik dönemi eşleştirilebilir.

#### İş kuralları

| Kural | Açıklama |
|-------|----------|
| **Lisans süresi** | Her ücretli kurum için başlangıç ve bitiş tarihi set edilir (yıllık/aylık dönem). |
| **Erişim** | `subscription_end_date` geçene kadar (ve tahsilat tamamsa) tam erişim. |
| **Süre bitimi** | Bitiş tarihi geçince: önce **grace period** (örn. 7–14 gün) verilebilir; sonrasında **read-only** veya **giriş engeli**; kullanıcıya “Lisans süreniz sona erdi, yenilemek için iletişime geçin” benzeri mesaj. |
| **Tahsilat gecikmesi** | Ödeme vadesi geçtiği halde tahsilat yoksa: `subscription_status = grace`; grace süresi bitince `suspended`; ödeme alınınca `active` + `subscription_end_date` uzatılır. |
| **Yenileme** | Yeni dönem başlatılırken `subscription_end_date` bir dönem (ay/yıl) ileri alınır; fatura kesilir. |

#### Teknik geliştirmeler

1. **Auth / giriş kontrolü**  
   - Her girişte (ve gerekirse kritik API çağrılarında) tenant’ın `subscription_end_date` ve `subscription_status` kontrolü.  
   - `expired` veya grace süresi bitmiş `suspended` ise: demo’daki gibi lisans bitiş sayfasına yönlendirme veya API’de `403` + uygun mesaj.

2. **Lisans bitiş / yenileme sayfası**  
   - Web/mobil: “Lisans süreniz sona ermiştir. Yenilemek için lütfen bizimle iletişime geçin.” + iletişim bilgisi.  
   - İsteğe bağlı: “Yakında süreniz dolacak” uyarısı (örn. 30 gün, 7 gün kala).

3. **Bildirimler**  
   - Yenileme tarihinden X gün önce (örn. 30, 14, 7) e-posta veya uygulama içi hatırlatma.  
   - Vade geçtiği halde ödeme alınmamışsa: kurum iletişim kişisine hatırlatma; platform yöneticisine “Ödemesi gecikmiş kurumlar” listesi.

4. **Platform yönetim paneli**  
   - **Lisans süre takibi:** Tüm kurumlar (demo hariç) için `subscription_start_date`, `subscription_end_date`, kalan gün, `subscription_status`.  
   - Filtreler: “Yakında bitecekler”, “Süresi dolmuşlar”, “Grace / ödemesi gecikmiş”.  
   - Aksiyonlar: “Lisansı yenile” (yeni bitiş tarihi + dönem), “Grace uzat”, “Askıya al / askıdan kaldır”.

5. **Raporlama**  
   - Dönem bazında aktif kurum sayısı, yenileme oranı, süre dolumu/grace/suspended sayıları (opsiyonel).

#### Demo ile ortak kullanım

- Hem demo hem ücretli için **tek bir “erişim geçerlilik” mantığı** kullanılabilir:  
  - Demo: `demo_end_date` + `is_demo`  
  - Ücretli: `subscription_end_date` + `subscription_status`  
- Girişte önce demo/ücretli ayrımı, sonra ilgili bitiş tarihi ve durum kontrolü yapılır; süre/durum uygunsa erişim verilir, değilse aynı türde “süre bitiş / yenileme” sayfasına yönlendirilir.

Bu yapı ile **genel kullanım** için de lisanslama ve süre takibi demo ile tutarlı ve yönetilebilir hale getirilmiş olur.

### 5.3 Self-Service ve Yönetim Paneli

- **Kurum yöneticisi:** Kendi kullanıcılarını, rollerini ve temel ayarlarını yönetir.  
- **Platform yöneticisi (SaaS operatör):** Tenant listesi, plan atama, limitler, kurum bazında fatura/ödeme takibi, genel ayarlar, basit metrikler.  
- **Denetim:** Kim, ne zaman, hangi tenant’ta ne yaptı – sorgulanabilir log.

### 5.4 Demo (3 Aylık Deneme) Sunumu

Uygulamaların (HEDAS, EDTS) potansiyel müşterilere **3 aylık demo** olarak sunulması için aşağıdaki geliştirme önerileri uygulanabilir.

#### Amaç

- Yeni kurumların yazılımı **ödeme taahhüdü olmadan** deneyebilmesi.
- Demo süresi bitiminde **lisanslı aboneliğe geçiş** veya **erişim kısıtlaması** (read-only / kilit ekranı).
- Kurum bazında demo açılışı, süre takibi ve operasyonel süreçlerin netleştirilmesi.

#### Veri modeli önerisi

- **Tenant (kurum) kaydında:** `is_demo` (veya `subscription_type`: demo / paid), `demo_start_date`, `demo_end_date`.
- **Abonelik durumu:** `active` (demo veya ücretli), `demo_expired`, `suspended`, `cancelled`.
- Demo süresi varsayılan **90 gün (3 ay)**; gerekirse kurum bazında farklı süre tanımlanabilir.

#### İş kuralları

| Kural | Açıklama |
|-------|----------|
| **Demo süresi** | 3 ay (90 gün); başlangıç tarihi kurum eklendiğinde veya “demo aktif” işlendiğinde set edilir. |
| **Erişim** | Demo süresince tam işlevsel erişim (HEDAS/EDTS plana göre); limitler opsiyonel (örn. kullanıcı sayısı, depolama sınırı). |
| **Süre bitimi** | `demo_end_date` geçince oturum açılışında uyarı; erişim **read-only** veya **giriş engeli** (yapılandırmaya göre). |
| **Geçiş** | Demo’dan ücretli aboneliğe: plan atanır, `is_demo` kaldırılır, fatura süreci başlar. Mevcut veri korunur. |

#### Teknik geliştirmeler

1. **Auth / giriş kontrolü**  
   - Her girişte veya API çağrısında tenant’ın `demo_end_date` ve `is_demo` kontrolü.  
   - Süre dolmuşsa: yönlendirme (demo bitiş sayfası) veya API’de `403` + mesaj (“Demo süreniz sona erdi”).

2. **Demo bitiş sayfası**  
   - Web: “Demo süreniz sona erdi. Lisanslı kullanım için bizimle iletişime geçin.” + iletişim bilgisi.  
   - Mobil: Aynı mesaj + gerekirse deep link / iletişim butonu.

3. **Bildirimler**  
   - Demo bitimine X gün kala (örn. 7 gün, 1 gün) e-posta veya uygulama içi bilgilendirme.  
   - İsteğe bağlı: platform yöneticisine “Yakında bitecek demolar” listesi.

4. **Platform yönetim paneli**  
   - Demo kurumlar listesi: başlangıç/bitiş tarihi, kalan gün.  
   - “Demo’yu uzat” veya “Ücretli plana geçir” aksiyonları.  
   - Demo açılışı: yeni tenant oluşturulurken “Demo (3 ay)” seçeneği.

5. **Raporlama**  
   - Demo kullanım metrikleri (aktif kullanıcı, giriş sayısı, HEDAS/EDTS kullanım) – dönüşüm analizi için.

#### Operasyonel süreç

- **Demo talebi:** Satış/operasyon ekibi yeni kurum ekler; “Demo – 3 ay” atanır, başlangıç tarihi set edilir.  
- **Süre içi:** Kullanıcılar normal kullanır; bildirimler otomatik gider.  
- **Süre bitimi:** Sistem erişimi kısıtlar veya kilit ekranı gösterir.  
- **Dönüşüm:** Kurum lisanslı plana geçerse fatura kesilir, `is_demo` kaldırılır, `demo_end_date` devre dışı bırakılır.

#### Fazlara dağılım

- **Faz 1:** Tenant’ta `is_demo`, `demo_start_date`, `demo_end_date` alanları; girişte süre kontrolü (basit).  
- **Faz 2:** Demo bitiş sayfası, kısa bildirim (e-posta veya in-app).  
- **Faz 3:** Yönetim panelinde demo listesi, “uzat / ücretli plana geçir”, demo kullanım metrikleri.

Bu yapı ile uygulamalar tutarlı şekilde **3 aylık demo** olarak sunulabilir ve süre sonunda kurum bazında lisanslı modele geçiş yönetilebilir.

---

## 6. Güvenlik ve Uyumluluk

- **KVKK:** Kişisel veri işleme kayıtları, silme/güncelleme talepleri, veri minimizasyonu.  
- **Şifreleme:** Veri iletiminde TLS; hassas alanlar (opsiyonel) DB’de şifreli.  
- **Güvenli geliştirme:** Bağımlılık taraması, düzenli güncelleme, SQL injection/XSS koruması (mevcut iyileştirmelerle uyumlu).

---

## 7. Operasyon ve Gözlemlenebilirlik

- **Loglama:** Uygulama ve erişim logları; tenant_id ve user_id her zaman.  
- **Metrik:** İstek sayısı, hata oranı, yanıt süresi (basit dashboard).  
- **Hata izleme:** Sentry/benzeri; production’da stack trace’ler güvenli ve sadece yetkili ekip görür.  
- **Yedekleme:** Veritabanı ve kritik dosyalar düzenli yedeklenir; tenant bazlı geri yükleme senaryosu tanımlanır.

---

## 8. Uygulama Fazları (Yol Haritası)

### Faz 1 – Temel (3–6 ay)

- API standardizasyonu: ortak format, hata yapısı, sayfalama.  
- JWT (veya token) tabanlı auth; mevcut login’e token üretimi eklenmesi.  
- Tenant altyapısı: `tenant_id` eklenmesi, mevcut verilerin tek varsayılan tenant’a atanması.  
- **Demo altyapısı:** Tenant’ta `is_demo`, `demo_start_date`, `demo_end_date`; girişte demo süre kontrolü.  
- Dokümantasyon: API katalog (endpoint listesi, auth, örnek istekler).

### Faz 2 – Ürün ve Erişim (6–12 ay)

- HEDAS ve EDTS için mobil uygulama (tek app, iki mod); token auth ile mevcut API kullanımı.  
- Self-service: kurum yöneticisi kullanıcı/rol yönetimi.  
- Abonelik/plan modeli: veritabanında plan ve limitler; uygulama tarafında kontrol.  
- **Lisans süre takibi (genel kullanım):** Tenant’ta `subscription_start_date`, `subscription_end_date`, `subscription_status`; girişte süre ve durum kontrolü; lisans bitiş sayfası.  
- **Demo:** Demo bitiş sayfası (web/mobil), demo süresi dolunca erişim kısıtı; isteğe bağlı e-posta / in-app bildirim (bitiş öncesi uyarı).

### Faz 3 – SaaS Olgunluk (12–18 ay)

- Platform yönetim paneli (tenant, plan, limit, basit metrik).  
- **Demo yönetimi:** Demo kurumlar listesi (başlangıç/bitiş, kalan gün), “Demo uzat”, “Ücretli plana geçir”; demo kullanım metrikleri.  
- **Genel kullanım lisans yönetimi:** Lisans süre listesi (bitiş tarihi, kalan gün, durum), “Yakında bitecekler / Süresi dolanlar / Grace”, “Lisansı yenile”, “Grace uzat”; yenileme öncesi bildirimler.  
- **Kurumsal fatura ve kurum bazında ödeme takibi:** Fatura kesimi (dönemsel), tahsilat durumu, vade takibi; online ödeme entegrasyonu yok.  
- SSO (SAML/OIDC) – ihtiyaca göre.  
- Gelişmiş raporlama ve AI kullanım alanlarının genişletilmesi.

### Faz 4 – Ölçek ve İleri (18+ ay)

- Performans: cache (Redis), sorgu optimizasyonu, gerekirse read replica.  
- Kritik servislerin ayrılması (auth, tenant, fatura/lisans takibi).  
- Çok bölgeli veya yüksek kullanıcı sayısı için mimari gözden geçirme.

---

## 9. Başarı Kriterleri

- Tüm kritik işlemlerin API üzerinden yapılabilmesi.  
- Mobil uygulamanın production’da kullanılması.  
- Multi-tenant veri izolasyonunun kanıtlanması (test ve denetim).  
- Yeni bir kurumun (tenant) operasyonel süreçlerle eklenebilmesi.  
- **Demo:** 3 aylık demo kurumlarının sorunsuz açılıp, süre bitiminde erişim kısıtlaması ve ücretli plana geçiş akışının çalışması.  
- **Genel kullanım:** Ücretli kurumlar için lisans süre takibinin (başlangıç/bitiş, yenileme, grace, süre bitiminde kısıtlama) çalışması.  
- Güvenlik ve KVKK ile uyum için temel kontrollerin yerinde olması.

---

## 10. Özet

Bu planda:

- **Mevcut yapı korunuyor**, aşamalı olarak API-first ve multi-tenant hale getiriliyor.  
- **SaaS:** Çoklu kiracı, abonelik/plan, **kurumsal fatura ve kurum bazında ödeme** (online ödeme yok), **3 aylık demo (deneme)** sunumu, self-service ve (ileride) platform yönetim paneli hedefleniyor.  
- **Kurumsal:** Güvenlik, audit, KVKK ve operasyonel kontroller plana dahil.  
- **Mobil ve AI:** Mevcut backend kullanılarak mobil uygulama ve AI özellikleri genişletiliyor.

Doküman, proje sahibi ve teknik ekip için referans alınacak “SaaS odaklı kurumsal geliştirme” çerçevesi olarak kullanılabilir; her faz kendi içinde daha detaylı teknik şartname ve sprint planlarına ayrıştırılabilir.

---

## 11. Kullanıcıya Yenilik Önerileri

Kullanıcı deneyimini güçlendirmek ve "yenilik" hissi yaratmak için aşağıdaki fikirler değerlendirilebilir. Öncelik ihtiyaca ve kaynağa göre belirlenir.

### 11.1 Bildirimler ve Hatırlatıcılar

| Öneri | Açıklama | Değer |
|-------|----------|--------|
| **Duruşma hatırlatıcıları** | Yaklaşan duruşma tarihi için e-posta / push (örn. 1 gün önce, aynı sabah). | Unutmayı azaltır, takip kolaylaşır. |
| **Vade / görev hatırlatıcıları** | Belge vade tarihi, tutanak yükleme süresi vb. için hatırlatma. | İş takibini güçlendirir. |
| **Kişiselleştirilmiş bildirim ayarları** | Kullanıcı hangi olaylarda (yeni atama, yorum, vade) bildirim alacağını seçer. | Gürültüyü azaltır, ilgili bilgi artar. |
| **Aktivite özeti** | Günlük/haftalık "Bugün sizin için X duruşma, Y dosya güncellendi" özeti. | Farkındalık, kontrol hissi. |

### 11.2 AI ve Akıllı Özellikler

| Öneri | Açıklama | Değer |
|-------|----------|--------|
| **Doğal dil arama** | "Geçen ay davacı tarafındaki duruşmalar" gibi cümleyle arama; mevcut AI asistan ile genişletilebilir. | Hızlı bulma, raporlama. |
| **Otomatik özet / rapor** | Seçilen filtreye göre "Bu ay özeti", "Avukat bazlı özet" gibi kısa metin; Gemini ile. | Yönetim raporu için zaman kazancı. |
| **Tahmine dayalı uyarılar** | Yoğun dönem tahmini, "Önümüzdeki 2 hafta duruşma yoğun" gibi basit uyarı. | Kapasite planlaması. |
| **Belge/dosya özeti** | Yüklenen evrakın AI ile özeti veya anahtar bilgi çıkarımı (opsiyonel, KVKK'ya uygun). | Hızlı inceleme. |

### 11.3 Görselleştirme ve Raporlama

| Öneri | Açıklama | Değer |
|-------|----------|--------|
| **Takvim görünümü** | Duruşmalar takvimde; haftalık/aylık, tıklayınca detay. | Zaman planlaması. |
| **Özelleştirilebilir dashboard** | Kullanıcının widget'ları sürükleyip bırakması, kendi "ana ekran"ını oluşturması. | Kişisel verim. |
| **Kayıtlı filtreler** | Sık kullanılan arama/filtre kombinasyonunun "Favori" olarak kaydedilmesi, tek tıkla uygulanması. | Tekrarlayan işleri hızlandırır. |
| **Tek tıkla dışa aktarma** | Listelerden PDF/Excel export; e-posta ile gönder veya indir. | Raporlama, paylaşım. |

### 11.4 İş Birliği ve İş Akışı

| Öneri | Açıklama | Değer |
|-------|----------|--------|
| **Dosya/duruşma yorumları** | Belirli kayıt üzerinde not/yorum; "X avukat şunu ekledi" geçmişi. | Ekip içi iletişim. |
| **Görev / atama** | Duruşma veya dosyaya "Y kişisine yapılacak iş atandı" ve basit durum (bekliyor / yapıldı). | Sorumluluk netleşir. |
| **Ortak aktivite akışı** | "Son yapılanlar" listesi (kim, ne kaydında, ne yaptı); kurum içi şeffaflık. | Takip ve güven. |

### 11.5 Mobil ve Günlük Kullanım

| Öneri | Açıklama | Değer |
|-------|----------|--------|
| **Mobil hızlı arama** | Uygulama açıldığında arama çubuğu; esas no, dosya no, taraf adı ile anında arama. | Sahada / mahkeme öncesi hızlı erişim. |
| **Favoriler / pin'leme** | Sık kullanılan dosya veya duruşmanın "favori"e eklenmesi, listede üstte görünmesi. | Erişim hızı. |
| **Karanlık mod** | Göz yormayan tema seçeneği. | Uzun süre kullanım konforu. |
| **Çevrimdışı görüntüleme (sınırlı)** | Son açılan listelerin/detayların cache'lenmesi; bağlantı yokken sadece okuma (ileride). | Alanında kullanım. |

### 11.6 Entegrasyonlar

| Öneri | Açıklama | Değer |
|-------|----------|--------|
| **Takvim senkronizasyonu** | Duruşma tarihlerinin Google/Outlook takvimine (sadece okuma) aktarılması. | Tek takvimde toplama. |
| **E-imza / e-arşiv** | Uygun senaryolarda e-imza veya e-arşiv fatura entegrasyonu (ihtiyaca göre). | Kurumsal süreçlerle uyum. |
| **E-Devlet (opsiyonel)** | Sadece okuma, yetkili servisler üzerinden bilgi çekme (mevzuat ve API'ye bağlı). | Veri girişi azalır. |

### 11.7 Şeffaflık ve Güven

| Öneri | Açıklama | Değer |
|-------|----------|--------|
| **"Benim yaptıklarım" geçmişi** | Kullanıcının kendi yaptığı değişikliklerin listesi (ne zaman, hangi kayıtta). | Bireysel denetim. |
| **Basit onay/red akışı** | Kritik işlemlerde (örn. silme, arşiv) onay penceresi veya iki aşamalı doğrulama. | Yanlışlıkla silmeyi azaltır. |
| **Bilgilendirici boş sayfalar** | Veri yokken "Henüz kayıt yok; şunu yaparak başlayabilirsiniz" + kısa rehber. | Yeni kullanıcı deneyimi. |

### 11.8 Tarayıcı (Scanner) Entegrasyonu – Evrak Doğrudan Modal Akışı

**Hedef senaryo:** Kullanıcı fiziksel tarayıcıda (USB veya ağ) "Tara" / "Evrak tara" ile tarama yapar; HEDAS ve EDTS içinde **dosya seçme/yükleme adımı olmadan** taranan evrak doğrudan programda bir **modal**'da açılır, düzenlenip kaydedilir.

#### Akış (adım adım)

1. **Tara**  
   Kullanıcı, **fiziksel tarayıcıda** (USB bağlantılı veya ağ üzerinden erişilen) "Tara" / "Evrak tara" butonuna basar.

2. **Modal açılır**  
   HEDAS/EDTS arayüzünde **dosya seçmeden veya manuel yükleme yapmadan** taranan evrak doğrudan bir **modal** pencerede açılır (görüntü + form).

3. **Görüntüden gelen bilgi + form**  
   - Görüntü işlenir (OCR + isteğe bağlı AI ile alan çıkarma).  
   - Çıkan bilgiler ve **ek alanlar** modal içindeki **formda** gösterilir.  
   - Kullanıcı alanları **düzenler** veya **ek bilgi girer**.

4. **Kaydet**  
   "Kaydet" ile hem **evrak (görüntü/PDF)** hem **form verisi** programa (HEDAS veya EDTS’e göre ilgili modüle) kaydedilir.

#### Teknik gereksinimler (kısa)

- **Tarayıcıdan görüntüyü almak:**  
  - **USB tarayıcı:** Tarayıcı doğrudan tarayıcıya (browser) bağlanamadığı için araya **yerel bir aracı** (TWAIN/SANE kullanan küçük uygulama) konur. Kullanıcı web arayüzünde "Evrak tara" der; aracı tarama yapar, görüntüyü sayfaya veya sunucuya iletir; modal açılır.  
  - **Ağ tarayıcısı:** Tarayıcı "Scan to URL" / "Scan to folder" ile sunucuya veya paylaşıma atar; backend yeni dosyayı alıp kullanıcı oturumuna bağlar; tarayıcıda bildirim + modal açılır.  
- **Frontend:** "Evrak tara" tetikleyicisi, gelen görüntüyü alan modal (önizleme + form), validasyon, "Kaydet" ile API çağrısı.  
- **Backend:** Görüntü/PDF kabulü, isteğe bağlı OCR/AI alan çıkarma, HEDAS/EDTS veri modeline kayıt (dosya + ek alanlar).

#### HEDAS / EDTS’te kapsam

- **HEDAS:** Taranan evrak yeni dosya/evrak kaydı veya mevcut dosyaya ek belge olarak kaydedilebilir; form alanları (dosya türü, mahkeme, davacı/davali, tarih vb.) modal’da doldurulur.  
- **EDTS:** Taranan evrak duruşma/dosya ile ilişkili belge (tutanak, celse evrakı vb.) olarak kaydedilebilir; modal’da esas no, duruşma tarihi, açıklama vb. ek alanlar bulunur.

Bu senaryo, **tarayıcı entegrasyonu** ile "tara → modal’da aç → düzenle → kaydet" akışının HEDAS ve EDTS’te standart özellik olarak sunulması hedefine göre planlanabilir.

---

**Önceliklendirme önerisi:** Önce **bildirim/hatırlatıcı** ve **kayıtlı filtre / tek tık export** gibi hızlı kazanımlar; ardından **takvim görünümü** ve **AI özet/rapor**; sonra **tarayıcı entegrasyonu (evrak tara → modal’da aç → düzenle → kaydet)** ve iş birliği (yorum, atama) ile diğer entegrasyonlar. Her madde ayrı bir iş paketi olarak planlanıp roadmap'e eklenebilir.
