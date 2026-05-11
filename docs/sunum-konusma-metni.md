# Akıllı Toplu Taşıma Prototipi - 8-10 Dakikalık Konuşma Metni

Bu metin, yazılımdan çok anlamayan bir kitleye uygulamayı anlaşılır şekilde anlatmak için hazırlanmıştır. Akış: iş değeri -> sistemin çalışma mantığı -> kodların yeri -> canlı veri akışı -> test ve güven.

## 0) Konuşmaya Başlamadan Önce (30 saniye)

Bugün size Manisa için geliştirdiğimiz Akıllı Toplu Taşıma Canlı Takip prototipini anlatacağım.
Bu bir üretim sistemi değil; gerçek GPS cihazı yerine simülasyon kullanan, ama gerçek bir sisteme dönüşebilecek bir karar destek prototipi.
Amacımız şu soruya cevap vermek: "Otobüsler nerede, duraklar ne kadar yoğun ve operasyon tarafında hangi aksiyon önerilmeli?"

## 1) Açılış - Prototipin Amacı (1 dakika)

Bu uygulamanın iş değeri üç noktada toplanıyor:
- Yolcu tarafı: Durakta ne kadar bekleyeceğini ve yoğunluğu görür.
- Operasyon tarafı: Hangi hatta takviye araç gerektiğini daha hızlı fark eder.
- Yönetim tarafı: Canlı ve özetlenmiş bir panel üzerinden şehir içi hareketi tek ekrandan izler.

Kısacası bu prototip, "ham veriyi" karar verilebilir bilgiye dönüştürüyor.

## 2) Kullanıcı Gözüyle Ekran Turu (1.5 dakika)

Ekranda iki ana alan var:
- Sol panel: hat sayısı, durak sayısı, aktif otobüs sayısı, ortalama yoğunluk, öneriler, hat listesi ve canlı otobüs listesi.
- Sağ alan: Leaflet tabanlı canlı harita.

Harita üstünde:
- Hat çizgileri farklı renklerle gösteriliyor.
- Duraklar yoğunluğa göre renkleniyor.
- Otobüs ikonları canlı olarak hareket ediyor.
- Bir durağa tıklanınca yolcu bilgilendirme paneli açılıyor: ETA, yoğunluk ve tahmini yolcu sayısı gösteriliyor.

Bu ekranın amacı teknik detay göstermek değil, "ne oluyor" sorusuna 5 saniyede cevap vermek.

## 3) Verinin Hikayesi - Simülasyon, ETA ve Canlı Yayın (2 dakika)

Bu prototipte veri akışı şöyle:
1. Önce seed ile örnek şehir verisi oluşturuluyor: 5 hat, 50 durak, 10 otobüs.
2. `simulate:buses` komutu sürekli çalışıp otobüs konumlarını güncelliyor.
3. Her güncellemede bir event yayınlanıyor: `BusPositionUpdated`.
4. Frontend, route kanalından bu event'i dinleyip marker konumunu animasyonla güncelliyor.
5. Ek olarak her 30 saniyede bir `layers` API çağrısıyla durak yoğunluğu, ETA ve operasyon önerileri tazeleniyor.

ETA hesabı tamamen anlaşılır bir formüle dayanıyor:
- Mesafe
- Hız
- Duraklar arası ortalama süre
- Yoğunluk cezası

Yani model "black-box" değil, yorumlanabilir bir yaklaşım kullanıyor.

## 4) Kodlar Nerede? (2.5 dakika)

Bu bölümde "hangi kod nerede" sorusunu hızlıca kapatıyoruz.

### 4.1 Giriş Noktaları
- `routes/web.php`
  - `/` -> ana harita sayfası
  - `/api/live-map/bootstrap` -> açılışta otobüsleri getirir
  - `/api/live-map/layers` -> hat, durak, ETA, yoğunluk verisini getirir
- `routes/channels.php`
  - `routes.{routeId}` -> canlı yayın kanalı

### 4.2 Backend (Laravel)
- `app/Http/Controllers/TransitMapController.php`
  - API çıktısını hazırlar: bus listesi, route katmanları, analitik özetler
- `app/Domain/Transit/Services/Simulation/BusSimulationEngine.php`
  - her tikte otobüsü hareket ettirir, loglar, event yayınlar
- `app/Domain/Transit/Services/Simulation/RouteInterpolator.php`
  - polyline üstünde yeni konumu matematiksel olarak hesaplar
- `app/Domain/Transit/Services/Eta/StopEtaService.php`
  - her durak için en yakın/uygun ETA'yı bulur
- `app/Domain/Transit/Services/Eta/EtaEstimator.php`
  - ETA formülünü uygular
- `app/Events/BusPositionUpdated.php`
  - WebSocket ile frontend'e anlık veri taşır

### 4.3 Frontend
- `resources/views/transit-map.blade.php`
  - sayfa iskeleti (sidebar, harita, bilgi panelleri)
- `resources/js/transit-map.js`
  - harita çizimi, marker yönetimi, kanal aboneliği, UI güncellemesi
- `resources/js/echo.js`
  - Laravel Echo + Reverb bağlantısı
- `resources/css/app.css`
  - görsel tasarım, responsive düzen, kart/panel stilleri

### 4.4 Veri Tabanı
- `database/migrations/*`
  - `routes`, `stops`, `buses`, `bus_logs`, `stop_densities` tabloları
- `database/seeders/TransitSimulationSeeder.php`
  - hatları, durakları, otobüsleri üretir
- `database/seeders/StopDensitySeeder.php`
  - saatlik yoğunluk ve yolcu tahmini üretir

### 4.5 Test
- `tests/Feature/TransitSimulationTest.php`
  - seed sonrası sayıların doğru oluştuğunu doğrular
  - `bootstrap` ve `layers` endpointlerinin şemasını doğrular
  - event içindeki temel alanları doğrular

## 5) Canlı Akış Senaryosu (1 dakika)

Demo sırasında şu 3 süreç paralel çalışır:
1. `php artisan serve --host=127.0.0.1 --port=8000`
2. `php artisan reverb:start --host=127.0.0.1 --port=8080`
3. `php artisan simulate:buses --interval=1`

Sonuç:
- Simülasyon konumu üretir.
- Reverb event'i yayınlar.
- Harita marker'ları canlı hareket eder.
- Operasyon paneli periyodik API ile güncellenir.

## 6) Güven ve Sınırlar (1 dakika)

Güven kısmı:
- Temel iş kuralları ve API sözleşmeleri testte doğrulanıyor.
- Veri modeli net ve okunabilir: rota-durak-otobüs ilişkisi açık.
- Hesaplama servisleri controller'dan ayrıldığı için bakım kolay.

Sınırlar (dürüstçe söylememiz gerekenler):
- Gerçek GPS cihazı yok, veri simüle.
- Yoğunluk gerçek sensörden değil, seed tabanlı modelden geliyor.
- Makine öğrenmesi katmanı henüz yok.

Yani bu sistem, "canlı prototip + karar destek demonstrasyonu" seviyesinde.

## 7) Kapanış (30 saniye)

Toparlarsak:
- Bu prototip, toplu taşımada canlı görünürlük sağlar.
- Yolcu bilgisi ve operasyon önerisini aynı ekranda birleştirir.
- Kod mimarisi, gerçek saha verisi geldiğinde büyümeye uygun tasarlanmıştır.

Bir sonraki doğal adım, simülasyon verisi yerine gerçek GPS/IoT akışını bağlamak ve öneri motorunu gerçek performans metrikleriyle doğrulamaktır.

---

## Sunumda Hızlı Cevap Kartı (Q&A için)

- "Bu canlı mı?" -> Evet, canlı akış var; fakat veri kaynağı simülasyon.
- "Neden önemli?" -> Bekleme süresi ve hat yoğunluğu görünür hale geliyor.
- "Kod bakımı kolay mı?" -> Evet, domain servisleri ayrık ve testleri mevcut.
- "Gerçeğe geçiş zor mu?" -> En kritik geçiş noktası veri kaynağı; UI ve akış mimarisi hazır.
