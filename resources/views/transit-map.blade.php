<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akıllı Toplu Taşıma - Canlı Harita</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<div class="layout" id="transitApp">
    <aside class="sidebar">
        <div class="brand-block">
            <span class="eyebrow">Manisa Büyükşehir</span>
            <h1>Canlı GPS Operasyon</h1>
            <p id="connectionStatus">Harita ve hat verisi yükleniyor.</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <span>Hat</span>
                <strong id="routeCount">0</strong>
            </div>
            <div class="stat-card">
                <span>Durak</span>
                <strong id="stopCount">0</strong>
            </div>
            <div class="stat-card">
                <span>Otobüs</span>
                <strong id="busCount">0</strong>
            </div>
            <div class="stat-card">
                <span>Ort. Yoğunluk</span>
                <strong id="densityAverage">0</strong>
            </div>
        </div>

        <div class="insight-panel">
            <span class="eyebrow">Simüle Edilmiş Talep Tahmini</span>
            <strong id="systemRecommendation">Veri bekleniyor</strong>
            <p id="systemInsight">Durak yoğunlukları ve TVS değerleri hesaplandığında operasyon önerisi burada görünecek.</p>
        </div>

        <div class="section-heading">
            <span>Operasyon Önerileri</span>
        </div>
        <div class="action-list" id="actionList"></div>

        <div class="section-heading">
            <span>Aktif Hatlar</span>
        </div>
        <div class="route-list" id="routeList"></div>

        <div class="section-heading">
            <span>Canlı Otobüsler</span>
        </div>
        <div class="bus-list" id="busList"></div>
    </aside>
    <main>
        <div class="map-topbar">
            <div>
                <span>Canlı izleme</span>
                <strong>GPS simülasyon akışı</strong>
            </div>
            <div>
                <span>Model</span>
                <strong>Talep + TVS karar desteği</strong>
            </div>
            <div>
                <span>Veri kaynağı</span>
                <strong>Simüle sensör verisi</strong>
            </div>
        </div>
        <div id="map"></div>
        <div class="passenger-panel" id="passengerPanel">
            <span class="eyebrow">Yolcu Bilgilendirme</span>
            <strong>Durak seçiniz</strong>
            <p>Haritadaki bir durağa tıklandığında yaklaşan otobüs, TVS ve yoğunluk uyarısı burada görünecek.</p>
        </div>
    </main>
</div>

</body>
</html>
