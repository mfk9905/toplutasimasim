# Mevcut Prototip Kapsamı

Bu doküman, mevcut sürümün kapsamını ve sınırlarını özetler.

## Kapsamda Olanlar

- Manisa özelinde 5 hat, 50 durak, 10 otobüs ile çalışan simülasyon
- Leaflet tabanlı canlı harita
- Laravel Reverb üzerinden gerçek zamanlı konum yayını
- Durak yoğunluğu ve yolcu tahmini gösterimi
- ETA (tahmini varış süresi) hesaplaması
- Hat bazlı operasyon önerisi üretimi

## Kapsam Dışında Olanlar

- Gerçek GPS cihazı veya CAN verisi entegrasyonu
- Gerçek yolcu sayım sensörü entegrasyonu
- Üretim ortamı ölçekleme ve yüksek erişilebilirlik kurgusu
- Makine öğrenmesi tabanlı talep tahmini

## Mimari Not

Uygulama, gerçek veri kaynakları sonradan eklenecek şekilde katmanlı tasarlanmıştır:
- Veri üretimi/simülasyon
- Domain servisleri (simülasyon + ETA)
- API ve event katmanı
- Harita tabanlı kullanıcı arayüzü
