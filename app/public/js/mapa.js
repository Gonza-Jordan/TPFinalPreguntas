document.addEventListener('DOMContentLoaded', function() {
    var map = L.map('map').setView([0, 0], 2);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        subdomains: 'abc'
    }).addTo(map);

    // Forzar el redimensionamiento cuando el mapa esté listo
    map.whenReady(function() {
        map.invalidateSize();
    });

    // Ajustar el tamaño del mapa al cambiar el tamaño de la ventana
    window.addEventListener('resize', function() {
        map.invalidateSize();
    });

    // Centrar y ajustar el mapa en cada cambio de zoom
    map.on('zoomend', function() {
        map.invalidateSize();
    });

    map.on('click', function(e) {
        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${e.latlng.lat}&lon=${e.latlng.lng}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('pais').value = data.address.country;
                document.getElementById('ciudad').value = data.address.city || data.address.town || 'Desconocido';
            });
    });
});
