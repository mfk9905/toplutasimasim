import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

const mapContainer = document.getElementById('map');

if (mapContainer) {
    const manisaCenter = [38.619099, 27.428921];
    const map = L.map('map', {
        zoomControl: true,
        preferCanvas: true,
    }).setView(manisaCenter, 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors',
    }).addTo(map);

    const markers = new Map();
    const routeMeta = new Map();
    const subscriptions = new Set();
    const routeLayers = new Map();
    const stopLayers = new Map();
    let etaRefreshTimer = null;
    let selectedStop = null;

    const busList = document.getElementById('busList');
    const routeList = document.getElementById('routeList');
    const routeCount = document.getElementById('routeCount');
    const stopCount = document.getElementById('stopCount');
    const busCount = document.getElementById('busCount');
    const densityAverage = document.getElementById('densityAverage');
    const connectionStatus = document.getElementById('connectionStatus');
    const actionList = document.getElementById('actionList');
    const systemRecommendation = document.getElementById('systemRecommendation');
    const systemInsight = document.getElementById('systemInsight');
    const passengerPanel = document.getElementById('passengerPanel');

    function setStatus(message) {
        if (connectionStatus) {
            connectionStatus.textContent = message;
        }
    }

    function routeColor(routeId, fallback = '#64748b') {
        return routeMeta.get(Number(routeId))?.color ?? fallback;
    }

    function busIcon(color) {
        return L.divIcon({
            className: '',
            html: `<div class="bus-icon" style="--route-color: ${color}"><span></span></div>`,
            iconSize: [30, 30],
            iconAnchor: [15, 15],
        });
    }

    function etaLabel(seconds) {
        if (seconds === null || seconds === undefined) {
            return 'Hesaplanıyor';
        }

        const mins = Math.max(1, Math.round(seconds / 60));
        return `${mins} dk`;
    }

    function averageEtaLabel(seconds) {
        return seconds === null || seconds === undefined ? 'TVS yok' : etaLabel(seconds);
    }

    function densityColor(score) {
        if (score >= 75) return '#ef4444';
        if (score >= 50) return '#f59e0b';
        if (score >= 30) return '#84cc16';
        return '#14b8a6';
    }

    function lastSeenLabel(value) {
        if (!value) {
            return 'şimdi';
        }

        const date = new Date(value);
        if (Number.isNaN(date.getTime())) {
            return 'şimdi';
        }

        return date.toLocaleTimeString('tr-TR', {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
        });
    }

    function renderStats(routes) {
        if (routeCount) {
            routeCount.textContent = String(routes.length);
        }

        if (stopCount) {
            const totalStops = routes.reduce((total, route) => total + (route.stops?.length ?? 0), 0);
            stopCount.textContent = String(totalStops);
        }

        if (busCount) {
            busCount.textContent = String(markers.size);
        }

        if (densityAverage) {
            const averageDensity = routes.length > 0
                ? routes.reduce((total, route) => total + Number(route.analytics?.average_density ?? 0), 0) / routes.length
                : 0;

            densityAverage.textContent = averageDensity.toFixed(0);
        }
    }

    function renderRouteList(routes) {
        if (!routeList) {
            return;
        }

        routeList.innerHTML = routes.map((route) => `
            <div class="route-item">
                <span class="route-swatch" style="background: ${route.color}"></span>
                <div>
                    <strong>${route.code}</strong>
                    <small>${route.name} | ${route.analytics?.density_level ?? 'Yoğunluk yok'}</small>
                </div>
                <em>${Number(route.analytics?.average_density ?? 0).toFixed(0)}%</em>
            </div>
        `).join('');
    }

    function renderActionList(routes) {
        if (!actionList) {
            return;
        }

        const sortedRoutes = [...routes].sort((a, b) => Number(b.analytics?.average_density ?? 0) - Number(a.analytics?.average_density ?? 0));
        const topRoute = sortedRoutes[0];

        if (systemRecommendation && topRoute) {
            systemRecommendation.textContent = topRoute.analytics?.operation_action ?? 'Operasyon verisi bekleniyor';
        }

        if (systemInsight && topRoute) {
            const peakStop = topRoute.analytics?.peak_stop?.name ?? 'durak verisi';
            systemInsight.textContent = `${topRoute.code} hattında ${peakStop} en yüksek talep noktasıdır. Model notu: ${topRoute.analytics?.model_note ?? 'simülasyon'}.`;
        }

        actionList.innerHTML = sortedRoutes.map((route) => `
            <div class="action-item">
                <span class="route-swatch" style="background: ${route.color}"></span>
                <div>
                    <strong>${route.code} | ${route.analytics?.operation_action ?? 'İzleniyor'}</strong>
                    <small>
                        Ort. yoğunluk ${Number(route.analytics?.average_density ?? 0).toFixed(0)}%
                        | Ort. TVS ${averageEtaLabel(route.analytics?.average_eta_seconds)}
                        | ${route.analytics?.feedback_summary ?? 'Geri bildirim yok'}
                    </small>
                </div>
            </div>
        `).join('');
    }

    function renderPassengerPanel(route, stop) {
        if (!passengerPanel) {
            return;
        }

        selectedStop = {
            routeId: Number(route.id),
            stopId: Number(stop.id),
        };

        passengerPanel.innerHTML = `
            <span class="eyebrow">Yolcu Bilgilendirme</span>
            <strong>${stop.name}</strong>
            <p>${route.code} hattı | TVS: ${etaLabel(stop.eta_seconds)} | Yoğunluk: ${stop.density_level} (${Number(stop.density_score).toFixed(0)}%)</p>
            <small>${Number(stop.passenger_estimate || 0)} tahmini yolcu. ${stop.density_score >= 70 ? 'Alternatif durak veya sonraki sefer önerilir.' : 'Durak kullanımı normal seviyede.'}</small>
        `;
    }

    function renderBusList() {
        if (!busList) {
            return;
        }

        const rows = Array.from(markers.values())
            .sort((a, b) => String(a.meta.route_code).localeCompare(String(b.meta.route_code)))
            .map((entry) => {
                const bus = entry.meta;
                const color = routeColor(bus.route_id, bus.route_color);
                const routeCode = bus.route_code ?? routeMeta.get(Number(bus.route_id))?.code ?? `Hat ${bus.route_id}`;

                return `
                    <div class="bus-item">
                        <span class="route-swatch" style="background: ${color}"></span>
                        <div>
                            <strong>${bus.plate_number}</strong>
                            <small>${routeCode} | ${Number(bus.speed_kmh).toFixed(0)} km/sa | ${bus.direction ?? bus.status ?? 'serviste'} | ${lastSeenLabel(bus.last_position_at)}</small>
                        </div>
                    </div>
                `;
            })
            .join('');

        busList.innerHTML = rows || '<div class="empty-state">Canlı otobüs verisi bekleniyor.</div>';

        if (busCount) {
            busCount.textContent = String(markers.size);
        }
    }

    function renderLayers(routes, shouldFitBounds = false) {
        routeLayers.forEach((layer) => map.removeLayer(layer));
        stopLayers.forEach((layer) => map.removeLayer(layer));
        routeLayers.clear();
        stopLayers.clear();
        routeMeta.clear();

        const bounds = L.latLngBounds([]);

        routes.forEach((route) => {
            routeMeta.set(Number(route.id), {
                code: route.code,
                name: route.name,
                color: route.color,
            });

            const points = (route.polyline || [])
                .map((point) => [Number(point.lat), Number(point.lng)])
                .filter(([lat, lng]) => !Number.isNaN(lat) && !Number.isNaN(lng));

            if (points.length > 1) {
                const polyline = L.polyline(points, {
                    color: route.color,
                    weight: 5,
                    opacity: 0.78,
                    lineCap: 'round',
                    lineJoin: 'round',
                }).addTo(map);

                polyline.bindTooltip(`${route.code} - ${route.name}`);
                routeLayers.set(route.id, polyline);
                points.forEach((point) => bounds.extend(point));
            }

            (route.stops || []).forEach((stop) => {
                const lat = Number(stop.lat);
                const lng = Number(stop.lng);
                const score = Number(stop.density_score ?? 0);

                if (Number.isNaN(lat) || Number.isNaN(lng)) {
                    return;
                }

                const circle = L.circleMarker([lat, lng], {
                    radius: 7,
                    color: route.color,
                    weight: 2,
                    fillColor: densityColor(score),
                    fillOpacity: 0.86,
                }).addTo(map);

                circle.bindPopup(
                    `<strong>${stop.name}</strong><br>` +
                    `${route.code} | Durak ${stop.sequence}<br>` +
                    `Yoğunluk: ${score.toFixed(0)} / 100 (${stop.density_level})<br>` +
                    `Tahmini yolcu: ${Number(stop.passenger_estimate || 0)}<br>` +
                    `TVS: ${etaLabel(stop.eta_seconds)}<br>` +
                    `Yolcu uyarısı: ${score >= 70 ? 'Yoğun durak' : 'Normal kullanım'}`,
                );
                circle.on('click', () => renderPassengerPanel(route, stop));

                stopLayers.set(stop.id, circle);
                bounds.extend([lat, lng]);
            });
        });

        if (shouldFitBounds && bounds.isValid()) {
            map.fitBounds(bounds.pad(0.12), {
                maxZoom: 14,
                animate: true,
            });
        }

        renderRouteList(routes);
        renderActionList(routes);
        renderStats(routes);
        refreshSelectedStop(routes);
        setTimeout(() => map.invalidateSize(), 100);
    }

    function refreshSelectedStop(routes) {
        if (!selectedStop) {
            return;
        }

        const route = routes.find((entry) => Number(entry.id) === selectedStop.routeId);
        const stop = route?.stops?.find((entry) => Number(entry.id) === selectedStop.stopId);

        if (route && stop) {
            renderPassengerPanel(route, stop);
        }
    }

    function animateMarker(marker, from, to, durationMs = 1150) {
        const startTime = performance.now();

        const step = (now) => {
            const t = Math.min(1, (now - startTime) / durationMs);
            const eased = t < 0.5 ? 2 * t * t : 1 - ((-2 * t + 2) ** 2) / 2;
            const lat = from.lat + (to.lat - from.lat) * eased;
            const lng = from.lng + (to.lng - from.lng) * eased;
            marker.setLatLng([lat, lng]);

            if (t < 1) {
                marker.__frameId = requestAnimationFrame(step);
            }
        };

        if (marker.__frameId) {
            cancelAnimationFrame(marker.__frameId);
        }

        marker.__frameId = requestAnimationFrame(step);
    }

    function upsertBus(bus) {
        const id = Number(bus.id);
        const lat = Number(bus.current_lat ?? bus.lat);
        const lng = Number(bus.current_lng ?? bus.lng);

        if (Number.isNaN(lat) || Number.isNaN(lng)) {
            return;
        }

        const color = routeColor(bus.route_id, bus.route_color);
        const route = routeMeta.get(Number(bus.route_id));
        const meta = {
            ...bus,
            route_code: bus.route_code ?? route?.code,
            route_name: bus.route_name ?? route?.name,
            route_color: color,
            last_position_at: bus.last_position_at ?? bus.timestamp ?? new Date().toISOString(),
        };

        if (!markers.has(id)) {
            const marker = L.marker([lat, lng], {
                icon: busIcon(color),
                zIndexOffset: 1000,
            }).addTo(map);

            marker.bindTooltip(`${meta.plate_number} | ${meta.route_code ?? 'Hat'}`, {
                direction: 'top',
                offset: [0, -14],
            });

            markers.set(id, {
                marker,
                meta,
            });
        } else {
            const existing = markers.get(id);
            const from = existing.marker.getLatLng();
            existing.marker.setIcon(busIcon(color));
            animateMarker(existing.marker, from, { lat, lng });
            existing.meta = { ...existing.meta, ...meta };
            existing.marker.setTooltipContent(`${existing.meta.plate_number} | ${existing.meta.route_code ?? 'Hat'}`);
        }

        renderBusList();
        scheduleEtaRefresh();
    }

    function scheduleEtaRefresh() {
        if (etaRefreshTimer !== null) {
            return;
        }

        etaRefreshTimer = window.setTimeout(() => {
            etaRefreshTimer = null;
            refreshLayers().catch(() => {});
        }, 2500);
    }

    function subscribeRoute(routeId) {
        if (subscriptions.has(routeId) || !window.Echo) {
            return;
        }

        subscriptions.add(routeId);
        window.Echo.channel(`routes.${routeId}`)
            .listen('.bus.position.updated', (payload) => {
                upsertBus({
                    id: payload.busId,
                    route_id: payload.routeId,
                    current_lat: payload.lat,
                    current_lng: payload.lng,
                    speed_kmh: payload.speedKmh,
                    timestamp: payload.timestamp,
                    plate_number: payload.plateNumber,
                    direction: payload.direction,
                    status: 'in_service',
                });
                setStatus('Canlı GPS akışı izleniyor.');
            });
    }

    async function bootstrapMap() {
        setTimeout(() => map.invalidateSize(), 0);

        const [{ data: layerData }, { data: bootstrapData }] = await Promise.all([
            window.axios.get('/api/live-map/layers'),
            window.axios.get('/api/live-map/bootstrap'),
        ]);

        const routes = layerData.routes ?? [];
        const buses = bootstrapData.buses ?? [];

        renderLayers(routes, true);
        routes.forEach((route) => subscribeRoute(route.id));
        buses.forEach((bus) => upsertBus(bus));

        setStatus(window.Echo ? 'Canlı GPS akışı hazır.' : 'Harita hazır, WebSocket bağlantısı bekleniyor.');
    }

    async function refreshLayers() {
        const { data } = await window.axios.get('/api/live-map/layers');
        renderLayers(data.routes ?? []);
    }

    bootstrapMap().catch(() => {
        setStatus('Harita açıldı, veri servisi bekleniyor.');
        renderBusList();
    });

    setInterval(() => {
        refreshLayers().catch(() => {});
    }, 10000);
}
