// Inicializar mapa (Leaflet gestiona los parámetros)
// {s}: Subdominio (a, b o c).
// {z}: Nivel de zoom.
// {x}: Coordenada horizontal del azulejo.
// {y}: Coordenada vertical del azulejo.

var map = L.map('map').setView([0, 0], 2);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

// Manejar selección de ubicación
map.on('click', function(e) {
    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${e.latlng.lat}&lon=${e.latlng.lng}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('pais').value = data.address.country;
            document.getElementById('ciudad').value = data.address.city || data.address.town;
        });
});
