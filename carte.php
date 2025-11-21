<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Carte interactive</title>

  <!-- CSS général -->
  <link rel="stylesheet" href="style.css">

  <!-- Leaflet LOCAL -->
  <link rel="stylesheet" href="leaflet/leaflet.css">
  <script src="leaflet/leaflet.js"></script>

  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>

<header>
  <nav>
    <h1 class="logo">Départ(ement)</h1>
    <ul>
      <li><a href="accueil.php">Accueil</a></li>
      <li class="active">Carte</li>
      <li><a href="apropos.html">À propos</a></li>
      <li><a href="contact.html">Contact</a></li>
    </ul>
  </nav>
</header>


<main>
  <section class="carte-section">

    <div class="carte-content">
      <div id="map"></div>
    </div>

  </section>
</main>


<!-- PANEL LATÉRAL -->
<div id="side-panel" class="side-panel">
  <button id="close-panel">×</button>
  <div id="panel-content">
    <p>Sélectionnez un département pour voir les détails</p>
  </div>
</div>



<script>
// ---------------------------------------
// INITIALISATION DE LA CARTE LEAFLET
// ---------------------------------------
var map = L.map('map', {
    minZoom: 5,
    maxZoom: 12,
    maxBounds: [[51.5, -5.5], [41, 10]],
    maxBoundsViscosity: 1.0
}).setView([46.8, 2.4], 6);

L.tileLayer('https://{s}.tile.openstreetmap.fr/osmfr/{z}/{x}/{y}.png', {
  attribution: '© OpenStreetMap contributors',
  noWrap: true
}).addTo(map);

// ---------------------------------------
// CHARGE LE GEOJSON LOCAL
// ---------------------------------------
$.getJSON('data/departements.geojson', function (geojson) {

  L.geoJSON(geojson, {

    onEachFeature: function (feature, layer) {

      layer.on('click', function () {

        let code_geo = feature.properties.code;
        let code_dep;

        if (code_geo === "2A") code_dep = 98;
        else if (code_geo === "2B") code_dep = 99;
        else code_dep = parseInt(code_geo);

        $.ajax({
          url: 'get_info.php',
          method: 'POST',
          data: { code_dep: code_dep },
          success: function (data) {
            console.log("Réponse AJAX :", data);
            showPanel(data);
          },
          error: function(err) {
            console.log("Erreur AJAX :", err);
            showPanel("<p>Erreur lors de la récupération des données.</p>");
          }
        });

      });

    },

    style: {
      color: "#333",
      weight: 1,
      fillColor: "#88c",
      fillOpacity: 0.5
    }

  }).addTo(map);

});

// ---------------------------------------
// PANEL LATÉRAL
// ---------------------------------------
const panel = document.getElementById('side-panel');
const content = document.getElementById('panel-content');
const closeBtn = document.getElementById('close-panel');

closeBtn.addEventListener('click', () => {
  panel.classList.remove('open');
});

function showPanel(data) {
  content.innerHTML = data;
  panel.classList.add('open');
}

</script>

</body>
</html>
