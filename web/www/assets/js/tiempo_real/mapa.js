document.addEventListener('DOMContentLoaded', function() {
    // Inicializar mapa centrado en México
    const map = L.map('map').setView([19.4326, -99.1332], 5);

    // Agregar capa de OpenStreetMap
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // Función para agregar marcador de entrega
    window.addDeliveryMarker = function(lat, lng, info) {
        const marker = L.marker([lat, lng]).addTo(map);
        marker.bindPopup(info);
        return marker;
    }

    // Función para dibujar ruta
    window.drawRoute = function(startLat, startLng, endLat, endLng) {
        const points = [
            [startLat, startLng],
            [endLat, endLng]
        ];
        return L.polyline(points, {
            color: 'blue',
            weight: 3,
            opacity: 0.7
        }).addTo(map);
    }

    // Ejemplo de marcadores de prueba
    addDeliveryMarker(19.4326, -99.1332, 'Pedido #001 - En camino');
    addDeliveryMarker(20.6597, -103.3496, 'Pedido #002 - En ruta');
    drawRoute(19.4326, -99.1332, 20.6597, -103.3496);
});