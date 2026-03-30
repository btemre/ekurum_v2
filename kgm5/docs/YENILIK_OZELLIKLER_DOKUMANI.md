# Yenilik Özellikleri – Özet Doküman

Bu doküman, eKurum KGM5 (HEDAS, EDTS) için planlanan dört ana yenilik alanını tek dosyada özetler: **Takvim Uygulaması**, **Mobil Uygulama**, **Evrak Tarama** ve **Yapay Zeka Entegrasyonu**. Her biri ayrı iş paketi / ürün yolu olarak ele alınabilir.

---

## 1. Takvim Uygulaması  
**Takvim üzerinden işlem, takip ve bildirim**

### Amaç
Duruşma ve vade tarihlerini takvim görünümünde göstermek; takvim üzerinden işlem yapmak, takibi kolaylaştırmak ve hatırlatıcı/bildirimlerle unutmayı azaltmak.

### Özellikler

| Özellik | Açıklama |
|--------|----------|
| **Takvim görünümü** | Duruşmalar haftalık/aylık takvimde; tıklayınca dosya/duruşma detayı açılır. |
| **Takvim üzerinden işlem** | Tarih seçerek yeni duruşma ekleme, mevcut duruşmayı taşıma/düzenleme. |
| **Takip** | Günlük/haftalık yoğunluk görünümü; hangi günlerde kaç duruşma/vade olduğu. |
| **Bildirim ve hatırlatıcılar** | Yaklaşan duruşma için e-posta / push (örn. 1 gün önce, aynı sabah); vade/görev hatırlatıcıları. |
| **Kişiselleştirilmiş ayarlar** | Kullanıcının hangi olaylarda (duruşma, vade, atama) bildirim alacağını seçmesi. |
| **Takvim senkronizasyonu (opsiyonel)** | Duruşma tarihlerinin Google/Outlook takvimine (sadece okuma) aktarılması; tek takvimde toplama. |

### Teknik not
- Mevcut duruşma/dosya API’leri kullanılır; takvim UI (haftalık/aylık) frontend’de; bildirimler e-posta ve/veya push servisi ile.

---

## 2. Mobil Uygulama  
**Android ve iOS**

### Amaç
HEDAS ve EDTS’e mobil cihazlardan (Android ve iOS) güvenli erişim; sahada, mahkeme öncesi veya hareket halinde hızlı bilgi erişimi ve temel işlemler.

### Özellikler

| Özellik | Açıklama |
|--------|----------|
| **Native / hybrid uygulama** | Android ve iOS için tek kod tabanı (React Native, Flutter vb.) veya ayrı native; mevcut web API’leri kullanılır. |
| **Kimlik doğrulama** | Web ile aynı oturum/SSO veya uygulama içi giriş; güvenli token yönetimi. |
| **Hızlı arama** | Uygulama açıldığında arama çubuğu; esas no, dosya no, taraf adı ile anında arama. |
| **Dosya ve duruşma listesi/detay** | Listeleme, filtreleme, detay görüntüleme; ekran boyutuna uyumlu. |
| **Favoriler / pin’leme** | Sık kullanılan dosya veya duruşmanın favoriye eklenmesi, listede üstte görünmesi. |
| **Bildirimler** | Push bildirimleri (duruşma hatırlatıcısı, atama, vade vb.); kullanıcı tercihine göre. |
| **Karanlık mod** | Göz yormayan tema seçeneği. |
| **Çevrimdışı görüntüleme (ileride)** | Son açılan listelerin/detayların önbelleğe alınması; bağlantı yokken sadece okuma. |

### Teknik not
- Backend mevcut API’ler; mobil uygulama API tüketicisi. Store yayını için sertifika, gizlilik metni ve uygulama açıklamaları gerekir.

---

## 3. Evrak Tarama  
**Taranan evrakı düzenleme ve kaydetme**

### Amaç
Fiziksel tarayıcıdan taranan evrakın, HEDAS/EDTS’te dosya seçmeden doğrudan modal’da açılması; kullanıcının düzenleyip kaydetmesi.

### Akış (özet)
1. Kullanıcı fiziksel tarayıcıda (USB veya ağ) **“Tara” / “Evrak tara”** ile tarama yapar.  
2. Taranan evrak **dosya seçme/yükleme adımı olmadan** programda bir **modal**’da açılır (görüntü + form).  
3. Görüntü işlenir (**OCR** ile metin çıkarma; isteğe bağlı AI ile alan eşleme); çıkan bilgiler ve **ek alanlar** formda gösterilir; kullanıcı **düzenler/ekler**.  
4. **“Kaydet”** ile evrak (görüntü/PDF) ve form verisi HEDAS veya EDTS’e kaydedilir.

### Özellikler

| Özellik | Açıklama |
|--------|----------|
| **Tarayıcı entegrasyonu** | USB: yerel aracı (TWAIN/SANE). Ağ: “Scan to URL” / “Scan to folder” ile sunucuya gönderim. |
| **Modal’da önizleme ve form** | Gelen görüntü önizleme; form alanları (dosya türü, mahkeme, davacı/davali, tarih, açıklama vb.). |
| **OCR** | Metin çıkarma için Tesseract (yerelde, ücretsiz) veya bulut OCR (Google Vision, Azure vb.). AI ayrı opsiyonel katman (alan eşleme). |
| **Kayıt** | HEDAS: yeni evrak veya mevcut dosyaya ek belge. EDTS: duruşma/dosyaya bağlı belge (tutanak vb.). |

### Teknik not
- Frontend: “Evrak tara” tetikleyicisi, modal (görüntü + form), validasyon, Kaydet API çağrısı.  
- Backend: Görüntü/PDF kabulü, OCR (ve isteğe bağlı AI), HEDAS/EDTS veri modeline kayıt.

---

## 4. Yapay Zeka Entegrasyonu  
**Doğal dil arama, otomatik özet/rapor, tahmine dayalı uyarılar, belge/dosya özeti**

### Amaç
Arama ve raporlamayı hızlandırmak, özet bilgi sunmak ve tahmine dayalı uyarılarla planlamayı desteklemek; KVKK ve veri güvenliği ile uyumlu kullanım.

### Özellikler

| Özellik | Açıklama |
|--------|----------|
| **Doğal dil arama** | “Geçen ay davacı tarafındaki duruşmalar”, “X mahkemesindeki bekleyen dosyalar” gibi cümleyle arama; mevcut AI asistan ile genişletilebilir; güvenli sorgu katmanı (Text-to-SQL vb.) kullanılır. |
| **Otomatik özet / rapor** | Seçilen filtreye göre “Bu ay özeti”, “Avukat bazlı özet” gibi kısa metin; LLM (örn. Gemini) ile üretilir. |
| **Tahmine dayalı uyarılar** | Yoğun dönem tahmini; “Önümüzdeki 2 hafta duruşma yoğun” gibi basit uyarı; kapasite planlaması için. |
| **Belge / dosya özeti** | Yüklenen evrakın AI ile özeti veya anahtar bilgi çıkarımı; KVKK ve yetkilendirme politikasına uygun. |

### Teknik not
- Doğal dil: güvenli Text-to-SQL veya benzeri katman; kullanıcı yetkisi dahilinde veri.  
- Özet/rapor ve belge özeti: LLM API (Gemini vb.); veri sunucuda işlenir, saklama ve loglama politikalara uygun yapılır.  
- Tahmine dayalı uyarı: basit istatistik/kurallar veya hafif model; duruşma yoğunluğu vb. veriye dayalı.

---

## Önceliklendirme (öneri)

| Sıra | Özellik alanı | Not |
|-----|----------------|-----|
| 1 | Takvim Uygulaması | Görünüm + bildirim/hatırlatıcı ile hızlı değer. |
| 2 | Evrak Tarama | OCR + modal akışı; TWAIN/SANE aracı ayrı iş paketi. |
| 3 | Yapay Zeka Entegrasyonu | Doğal dil arama ve özet önce; tahmin ve belge özeti sonra. |
| 4 | Mobil Uygulama | Android + iOS; API hazır olduktan sonra paralel ilerlenebilir. |

Her alan ayrı doküman (şartname, kullanıcı hikayeleri, teknik tasarım) ve roadmap maddesi olarak detaylandırılabilir.
