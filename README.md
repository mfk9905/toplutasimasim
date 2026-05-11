# Akıllı Toplu Taşıma Canlı Takip Prototipi

Bu proje, gerçek GPS ve saha yoğunluk verisi olmadan geliştirilen simülasyon tabanlı bir akıllı toplu taşıma prototipidir. Laravel, Reverb WebSocket yayını ve Leaflet haritası kullanılarak Manisa içinde 5 hat, 50 durak ve canlı hareket eden otobüsler gösterilir.

## Özellikler

- 5 hat ve toplam 50 durak içeren Manisa toplu taşıma simülasyonu
- Harita üzerinde canlı otobüs konumu takibi
- Laravel Reverb ile gerçek zamanlı WebSocket yayını
- Durak yoğunluğu, tahmini yolcu sayısı ve ETA gösterimi
- Kural tabanlı operasyon önerileri
- Yolcu bilgilendirme paneli

## Gereksinimler

Kurulumdan önce bilgisayarınızda aşağıdakiler yüklü olmalıdır:

- PHP 8.2 veya üzeri
- Composer
- Node.js 18 veya üzeri
- npm
- SQLite desteği etkin PHP kurulumu

PHP sürümünü kontrol etmek için:

```bash
php -v
```

Composer sürümünü kontrol etmek için:

```bash
composer -V
```

Node.js ve npm sürümlerini kontrol etmek için:

```bash
node -v
npm -v
```

## Kurulum

Projeyi klonlayın:

```bash
git clone <repo-url>
cd <repo-klasoru>
```

PHP bağımlılıklarını kurun:

```bash
composer install
```

Node.js bağımlılıklarını kurun:

```bash
npm install
```

Ortam dosyasını oluşturun:

Windows PowerShell:

```powershell
Copy-Item .env.example .env
```

macOS/Linux:

```bash
cp .env.example .env
```

Laravel uygulama anahtarını üretin:

```bash
php artisan key:generate
```

SQLite veritabanı dosyasını oluşturun:

Windows PowerShell:

```powershell
New-Item -ItemType File -Force database/database.sqlite
```

macOS/Linux:

```bash
touch database/database.sqlite
```

Veritabanını oluşturup örnek verileri yükleyin:

```bash
php artisan migrate:fresh --seed
```

Frontend dosyalarını derleyin:

```bash
npm run build
```

## Uygulamayı Çalıştırma

Uygulamanın tam çalışması için üç ayrı terminal açın.

1. Laravel web sunucusu:

```bash
php artisan serve --host=127.0.0.1 --port=8000
```

2. Reverb WebSocket sunucusu:

```bash
php artisan reverb:start --host=127.0.0.1 --port=8080
```

3. Otobüs simülasyonu:

```bash
php artisan simulate:buses --interval=1
```

Tarayıcıdan uygulamayı açın:

```text
http://127.0.0.1:8000
```

## Geliştirme Modu

Frontend üzerinde geliştirme yapacaksanız, production build yerine Vite geliştirme sunucusunu çalıştırabilirsiniz.

Ek terminalde:

```bash
npm run dev
```

Bu modda yine Laravel, Reverb ve simülasyon komutları açık olmalıdır.

## Test

Testleri çalıştırmak için:

```bash
php artisan test
```

Beklenen durum:

- Tüm testler başarılı olmalıdır.
- Seed sonrası 5 hat, 50 durak ve 10 otobüs oluşmalıdır.

## Veri Yapısı

Seed komutu aşağıdaki örnek verileri oluşturur:

- 5 toplu taşıma hattı
- Her hatta 10 durak
- Toplam 50 durak
- Her hatta 2 otobüs
- Saat bazlı simüle edilmiş durak yoğunluğu

## Önemli Notlar

- Bu proje gerçek GPS cihazı kullanmaz.
- Araç konumları simülasyon motoru tarafından üretilir.
- Yolcu yoğunluğu verileri gerçek sensörden değil, seed verilerinden simüle edilir.
- Python veya makine öğrenmesi katmanı bu prototipe henüz entegre edilmemiştir.
- Proje, gerçek saha verileriyle genişletilebilir bir karar destek prototipi olarak tasarlanmıştır.

## Sık Karşılaşılan Sorunlar

Harita açılıyor ama otobüsler hareket etmiyorsa:

```bash
php artisan simulate:buses --interval=1
```

Canlı veri gelmiyorsa Reverb sunucusunu kontrol edin:

```bash
php artisan reverb:start --host=127.0.0.1 --port=8080
```

Asset hatası alırsanız yeniden build alın:

```bash
npm run build
```

Verileri sıfırlamak isterseniz:

```bash
php artisan migrate:fresh --seed
```

## Proje Kapsamı

Prototip kapsamı ve gerçek sistemden farkları için:

```text
docs/mevcut-prototip-kapsami.md
```
