document.addEventListener('DOMContentLoaded', function () {
    const ports = Array.isArray(window.portTrackingData)
        ? window.portTrackingData
        : [];

    const mapElement = document.getElementById('trackingMap');
    const originSelect = document.getElementById('originPort');
    const destinationSelect = document.getElementById('destinationPort');
    const drawRouteButton = document.getElementById('drawRouteButton');
    const playTrackingButton = document.getElementById('playTrackingButton');
    const swapRouteButton = document.getElementById('swapRouteButton');
    const resetRouteButton = document.getElementById('resetRouteButton');
    const portTableSearch = document.getElementById('portTableSearch');
    const tablePortCounter = document.getElementById('tablePortCounter');
    const originInfo = document.getElementById('originInfo');
    const destinationInfo = document.getElementById('destinationInfo');
    const distanceInfo = document.getElementById('distanceInfo');

    if (!mapElement || !originSelect || !destinationSelect) {
        return;
    }

    if (typeof L === 'undefined') {
        console.error('Leaflet belum dimuat.');
        return;
    }

    const validPorts = ports.filter(function (port) {
        const latitude = Number(port.latitude);
        const longitude = Number(port.longitude);

        return Number.isFinite(latitude)
            && Number.isFinite(longitude)
            && latitude >= -90
            && latitude <= 90
            && longitude >= -180
            && longitude <= 180;
    });

    const portsById = new Map();

    validPorts.forEach(function (port) {
        portsById.set(String(port.id), port);
    });

    function getCountryName(port) {
        return port.country_real_name
            ?? port.country_name
            ?? '-';
    }

    function getPortLabel(port) {
        return `${port.name} — ${getCountryName(port)}`;
    }

    function escapeHtml(value) {
        return String(value ?? '-')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    function addPortOptions(selectElement) {
        validPorts
            .slice()
            .sort(function (first, second) {
                return getPortLabel(first).localeCompare(
                    getPortLabel(second),
                    'id'
                );
            })
            .forEach(function (port) {
                const option = document.createElement('option');
                option.value = String(port.id);
                option.textContent = getPortLabel(port);
                selectElement.appendChild(option);
            });
    }

    addPortOptions(originSelect);
    addPortOptions(destinationSelect);

    const map = L.map('trackingMap', {
        zoomControl: true,
        preferCanvas: true,
        worldCopyJump: true
    }).setView([10, 0], 2);

    L.tileLayer(
        'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
        {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }
    ).addTo(map);

    L.control.scale({
        imperial: false,
        position: 'bottomleft'
    }).addTo(map);

    const allPortsLayer = L.layerGroup().addTo(map);
    const routeLayer = L.layerGroup().addTo(map);
    const allPortMarkers = [];

    function getStatusColor(status) {
        if (status === 'Aman' || status === 'Normal') {
            return '#16a34a';
        }

        if (status === 'Waspada') {
            return '#f59e0b';
        }

        return '#dc2626';
    }

    validPorts.forEach(function (port) {
        const marker = L.circleMarker(
            [Number(port.latitude), Number(port.longitude)],
            {
                radius: 4,
                color: '#ffffff',
                weight: 1,
                fillColor: getStatusColor(port.status),
                fillOpacity: 0.68
            }
        )
            .bindTooltip(escapeHtml(port.name), {
                direction: 'top',
                opacity: 0.9
            })
            .addTo(allPortsLayer);

        allPortMarkers.push(marker);
    });

    function createBluePinIcon() {
        return L.divIcon({
            className: 'port-pin-wrapper',
            html: `
                <div class="port-pin">
                    <i class="bi bi-geo-alt-fill"></i>
                </div>
            `,
            iconSize: [34, 42],
            iconAnchor: [17, 40],
            popupAnchor: [0, -38]
        });
    }

    function createMovingDotIcon() {
        return L.divIcon({
            className: 'port-tracking-dot-wrapper',
            html: '<div class="port-tracking-dot"></div>',
            iconSize: [13, 13],
            iconAnchor: [6, 6]
        });
    }

    function buildPopup(port) {
        return `
            <div class="port-map-popup">
                <strong>${escapeHtml(port.name)}</strong><br>
                Kota: ${escapeHtml(port.city ?? '-')}<br>
                Negara: ${escapeHtml(getCountryName(port))}<br>
                Status: ${escapeHtml(port.status ?? 'Normal')}<br>
                Risiko: ${escapeHtml(port.port_risk_score ?? 0)}/100
            </div>
        `;
    }

    let originMarker = null;
    let destinationMarker = null;
    let routeLine = null;
    let movingDot = null;
    let animationId = null;
    let currentOrigin = null;
    let currentDestination = null;

    function toRadians(value) {
        return value * Math.PI / 180;
    }

    function calculateDistanceKm(start, end) {
        const earthRadiusKm = 6371;
        const latitudeDifference = toRadians(end.lat - start.lat);
        const longitudeDifference = toRadians(end.lng - start.lng);

        const calculation =
            Math.sin(latitudeDifference / 2) ** 2
            + Math.cos(toRadians(start.lat))
            * Math.cos(toRadians(end.lat))
            * Math.sin(longitudeDifference / 2) ** 2;

        return earthRadiusKm
            * 2
            * Math.atan2(
                Math.sqrt(calculation),
                Math.sqrt(1 - calculation)
            );
    }

    function stopAnimation() {
        if (animationId) {
            cancelAnimationFrame(animationId);
            animationId = null;
        }

        if (movingDot) {
            routeLayer.removeLayer(movingDot);
            movingDot = null;
        }
    }

    function clearRoute() {
        stopAnimation();
        routeLayer.clearLayers();

        originMarker = null;
        destinationMarker = null;
        routeLine = null;
    }

    function updateSelectedRows() {
        document.querySelectorAll('.port-row')
            .forEach(function (row) {
                row.classList.remove('is-origin', 'is-destination');

                if (
                    currentOrigin
                    && String(row.dataset.portId) === String(currentOrigin.id)
                ) {
                    row.classList.add('is-origin');
                }

                if (
                    currentDestination
                    && String(row.dataset.portId) === String(currentDestination.id)
                ) {
                    row.classList.add('is-destination');
                }
            });
    }

    function drawRoute() {
        const originId = originSelect.value;
        const destinationId = destinationSelect.value;

        if (!originId || !destinationId) {
            alert('Pilih pelabuhan asal dan tujuan terlebih dahulu.');
            return;
        }

        if (originId === destinationId) {
            alert('Pelabuhan asal dan tujuan harus berbeda.');
            playTrackingButton.disabled = true;
            return;
        }

        currentOrigin = portsById.get(String(originId));
        currentDestination = portsById.get(String(destinationId));

        if (!currentOrigin || !currentDestination) {
            alert('Koordinat pelabuhan tidak ditemukan.');
            return;
        }

        clearRoute();

        const originLatLng = L.latLng(
            Number(currentOrigin.latitude),
            Number(currentOrigin.longitude)
        );

        const destinationLatLng = L.latLng(
            Number(currentDestination.latitude),
            Number(currentDestination.longitude)
        );

        originMarker = L.marker(originLatLng, {
            icon: createBluePinIcon()
        })
            .bindPopup(buildPopup(currentOrigin))
            .addTo(routeLayer);

        destinationMarker = L.marker(destinationLatLng, {
            icon: createBluePinIcon()
        })
            .bindPopup(buildPopup(currentDestination))
            .addTo(routeLayer);

        routeLine = L.polyline(
            [originLatLng, destinationLatLng],
            {
                color: '#ef4444',
                weight: 3,
                opacity: 0.85,
                dashArray: '7, 9',
                lineCap: 'round'
            }
        ).addTo(routeLayer);

        const bounds = L.latLngBounds([
            originLatLng,
            destinationLatLng
        ]);

        map.fitBounds(bounds.pad(0.25), {
            maxZoom: 6
        });

        const distance = calculateDistanceKm(
            {
                lat: originLatLng.lat,
                lng: originLatLng.lng
            },
            {
                lat: destinationLatLng.lat,
                lng: destinationLatLng.lng
            }
        );

        originInfo.textContent = getPortLabel(currentOrigin);
        destinationInfo.textContent = getPortLabel(currentDestination);
        distanceInfo.textContent =
            `${distance.toLocaleString('id-ID', {
                maximumFractionDigits: 0
            })} km`;

        playTrackingButton.disabled = false;
        updateSelectedRows();
        originMarker.openPopup();
    }

    function playTracking() {
        if (!currentOrigin || !currentDestination || !routeLine) {
            drawRoute();

            if (!currentOrigin || !currentDestination || !routeLine) {
                return;
            }
        }

        stopAnimation();

        const start = L.latLng(
            Number(currentOrigin.latitude),
            Number(currentOrigin.longitude)
        );

        const end = L.latLng(
            Number(currentDestination.latitude),
            Number(currentDestination.longitude)
        );

        movingDot = L.marker(start, {
            icon: createMovingDotIcon(),
            interactive: false
        }).addTo(routeLayer);

        const duration = 7000;
        const startedAt = performance.now();

        playTrackingButton.disabled = true;

        function animate(currentTime) {
            const progress = Math.min(
                (currentTime - startedAt) / duration,
                1
            );

            const latitude =
                start.lat + (end.lat - start.lat) * progress;

            const longitude =
                start.lng + (end.lng - start.lng) * progress;

            movingDot.setLatLng([latitude, longitude]);

            if (progress < 1) {
                animationId = requestAnimationFrame(animate);
            } else {
                animationId = null;
                playTrackingButton.disabled = false;
                destinationMarker.openPopup();
            }
        }

        animationId = requestAnimationFrame(animate);
    }

    function resetRoute() {
        clearRoute();

        currentOrigin = null;
        currentDestination = null;

        originSelect.value = '';
        destinationSelect.value = '';

        originInfo.textContent = '-';
        destinationInfo.textContent = '-';
        distanceInfo.textContent = '- km';

        playTrackingButton.disabled = true;
        updateSelectedRows();

        if (allPortMarkers.length > 0) {
            const bounds = L.featureGroup(allPortMarkers).getBounds();

            if (bounds.isValid()) {
                map.fitBounds(bounds.pad(0.08), {
                    maxZoom: 5
                });
            }
        }
    }

    drawRouteButton?.addEventListener('click', drawRoute);
    playTrackingButton?.addEventListener('click', playTracking);

    swapRouteButton?.addEventListener('click', function () {
        const originValue = originSelect.value;

        originSelect.value = destinationSelect.value;
        destinationSelect.value = originValue;

        if (originSelect.value && destinationSelect.value) {
            drawRoute();
        }
    });

    resetRouteButton?.addEventListener('click', resetRoute);

    document.querySelectorAll('.set-origin-button')
        .forEach(function (button) {
            button.addEventListener('click', function () {
                originSelect.value = this.dataset.portId;
                currentOrigin = portsById.get(String(originSelect.value));

                if (
                    destinationSelect.value
                    && destinationSelect.value !== originSelect.value
                ) {
                    drawRoute();
                } else {
                    updateSelectedRows();
                }

                originSelect.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            });
        });

    document.querySelectorAll('.set-destination-button')
        .forEach(function (button) {
            button.addEventListener('click', function () {
                destinationSelect.value = this.dataset.portId;
                currentDestination = portsById.get(String(destinationSelect.value));

                if (
                    originSelect.value
                    && originSelect.value !== destinationSelect.value
                ) {
                    drawRoute();
                } else {
                    updateSelectedRows();
                }

                destinationSelect.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            });
        });

    portTableSearch?.addEventListener('input', function () {
        const keyword = this.value.toLowerCase().trim();
        let visibleCount = 0;

        document.querySelectorAll('.port-row')
            .forEach(function (row) {
                const visible =
                    keyword === ''
                    || row.dataset.search.includes(keyword);

                row.style.display = visible ? '' : 'none';

                if (visible) {
                    visibleCount++;
                }
            });

        if (tablePortCounter) {
            tablePortCounter.textContent = `${visibleCount} data`;
        }
    });

    if (allPortMarkers.length > 0) {
        const bounds = L.featureGroup(allPortMarkers).getBounds();

        if (bounds.isValid()) {
            map.fitBounds(bounds.pad(0.08), {
                maxZoom: 5
            });
        }
    }

    if (validPorts.length >= 2) {
        originSelect.value = String(validPorts[0].id);
        destinationSelect.value = String(
            validPorts[validPorts.length - 1].id
        );

        drawRoute();
    } else {
        drawRouteButton.disabled = true;
        playTrackingButton.disabled = true;
    }

    setTimeout(function () {
        map.invalidateSize();
    }, 250);
});