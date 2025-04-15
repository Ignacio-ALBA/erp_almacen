$(document).ready(function() {
    const map = L.map('map').setView([19.4326, -99.1332], 5);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    window.addDeliveryMarker = function(lat, lng, info) {
        const marker = L.marker([lat, lng]).addTo(map);
        marker.bindPopup(info);
    }

    window.drawRoute = function(startLat, startLng, endLat, endLng) {
        const points = [
            [startLat, startLng],
            [endLat, endLng]
        ];
        L.polyline(points, {
            color: 'blue',
            weight: 3,
            opacity: 0.7
        }).addTo(map);
    }

    // Test markers
    addDeliveryMarker(19.4326, -99.1332, 'Pedido #001 - En camino');
    addDeliveryMarker(20.6597, -103.3496, 'Pedido #002 - En ruta');
    drawRoute(19.4326, -99.1332, 20.6597, -103.3496);
});