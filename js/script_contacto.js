

    /* ===============================================
       FORMULARIO CONTACTO
    =============================================== */
    document.getElementById("contactForm").addEventListener("submit", function(e){
        e.preventDefault();
        alert("¡Gracias por contactarnos!");
        this.reset();
    });

    /* ===============================================
       NEWSLETTER
    =============================================== */
    document.getElementById("newsletterForm").addEventListener("submit", function(e){
        e.preventDefault();
        alert("Te has suscrito correctamente.");
        this.reset();
    });

    /* ===============================================
       CONFIGURACIÓN DEL MAPA
       Coordenadas actuales:
       Parque Xcaret
    ================================= 17.086815571082088, -96.7452099541727 ============= */
    const latitud  = 17.085954;
    const longitud = -96.745338;


    /* Crear mapa */
    const map = L.map('map').setView([latitud, longitud], 16);

    /* Fondo del mapa */
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{
        attribution:'© OpenStreetMap'
    }).addTo(map);

    /* Marcador */
    const marker = L.marker([latitud, longitud]).addTo(map);

    /* Popup */
    marker.bindPopup(`
        <b>📍 Ubicación</b><br>
        Latitud: ${latitud}<br>
        Longitud: ${longitud}
    `).openPopup();

    /* Círculo decorativo */
    L.circle([latitud, longitud],{
        color:'#2546F4',
        fillColor:'#2546F4',
        fillOpacity:0.15,
        radius:150
    }).addTo(map);

