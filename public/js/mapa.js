document.addEventListener('DOMContentLoaded', function() {
    // Inicializar el mapa con una vista predeterminada
    const map = L.map('map', {
        center: [20, 0],
        zoom: 2,
        minZoom: 2,
        maxZoom: 18,
        scrollWheelZoom: false
    });

    // Agregar el layer de OpenStreetMap
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 18,
        tileSize: 512,
        zoomOffset: -1
    }).addTo(map);

    // Variable para mantener el marcador actual
    let currentMarker = null;

    // Función para actualizar el marcador
    function updateMarker(latlng) {
        if (currentMarker) {
            map.removeLayer(currentMarker);
        }

        currentMarker = L.marker(latlng).addTo(map);

        // Hacer la solicitud a nuestro proxy en lugar de la API de Nominatim
        fetch(`/TPFinalPreguntas/public/proxy.php?lat=${latlng.lat}&lon=${latlng.lng}`)
            .then(response => response.json())
            .then(data => {
                const country = data.address.country || '';
                const city = data.address.city || data.address.town || data.address.village || data.address.suburb || '';

                document.getElementById('pais').value = country;
                document.getElementById('ciudad').value = city;

                currentMarker.bindPopup(`
            <strong>${city}</strong><br>
            ${country}
        `).openPopup();
            })
            .catch(error => {
                console.error('Error obteniendo la ubicación:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudo obtener la ubicación. Por favor, intenta de nuevo.'
                });
            });
    }

    // Evento de clic en el mapa
    map.on('click', function(e) {
        updateMarker(e.latlng);
    });

    // Asegurar que el mapa se renderice correctamente
    setTimeout(() => {
        map.invalidateSize();
    }, 100);

    // Actualizar el tamaño del mapa cuando se muestra el contenedor
    const observer = new MutationObserver(function(mutations) {
        map.invalidateSize();
    });

    // Observar cambios en la visibilidad del contenedor del mapa
    observer.observe(document.getElementById('map'), {
        attributes: true,
        childList: true,
        subtree: true
    });

    // Manejar cambios de tamaño de ventana
    window.addEventListener('resize', function() {
        map.invalidateSize();
    });

    // Agregar control de búsqueda básico
    const searchInput = document.createElement('input');
    searchInput.type = 'text';
    searchInput.placeholder = 'Buscar ubicación...';
    searchInput.className = 'form-control leaflet-bar';
    searchInput.style.position = 'absolute';
    searchInput.style.top = '10px';
    searchInput.style.right = '10px';
    searchInput.style.zIndex = '1000';
    searchInput.style.width = '200px';

    document.querySelector('.map-placeholder').appendChild(searchInput);

    // Funcionalidad de búsqueda
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            const query = this.value;
            fetch(`/TPFinalPreguntas/public/proxy.php?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.length > 0) {
                        const location = data[0];
                        const latlng = L.latLng(location.lat, location.lon);
                        map.setView(latlng, 13);
                        updateMarker(latlng);
                    }
                });
        }
    });
});
