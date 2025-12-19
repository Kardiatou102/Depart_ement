<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Carte interactive</title>

  <!-- CSS général -->
  <link rel="stylesheet" href="style.css" />

  <!-- Leaflet LOCAL -->
  <link rel="stylesheet" href="leaflet/leaflet.css" />
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

<main class="page-map">
  <section class="map-layout">

    <div class="map-card">
      <div id="map"></div>
      <div class="map-hint">Clique sur un département pour afficher ses infos</div>
    </div>

  </section>
</main>

<!-- Backdrop (fond sombre quand le panel est ouvert) -->
<div id="panel-backdrop" class="panel-backdrop" aria-hidden="true"></div>

<!-- PANEL LATÉRAL -->
<aside id="side-panel" class="side-panel" aria-hidden="true">
  <div class="panel-header">
    <button id="close-panel" class="panel-close" aria-label="Fermer">×</button>
    <div class="panel-title">
      <span class="panel-kicker">Département</span>
      <h2 class="panel-h2">Détails</h2>
    </div>
  </div>

  <div id="panel-content" class="panel-content">
    <p class="panel-muted">Sélectionnez un département pour voir les détails</p>
  </div>
</aside>

<script>
  // -----------------------------
  // INITIALISATION DE LA CARTE
  // -----------------------------
  const FR_BOUNDS = [[51.5, -5.5], [41, 10]];

  var map = L.map('map', {
    minZoom: 5,
    maxZoom: 12,
    maxBounds: FR_BOUNDS,
    maxBoundsViscosity: 1.0
  }).setView([46.8, 2.4], 6);

  L.tileLayer('https://{s}.tile.openstreetmap.fr/osmfr/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors',
    noWrap: true
  }).addTo(map);

  // -----------------------------
  // OPTION B : IMAGE OVERLAY (URL INTERNET)
  // -----------------------------
  function addMapImageOverlay() {
    // ✅ Image stable (Wikimedia). Tu peux la remplacer par une autre URL HTTPS.
    const imageUrl = "https://upload.wikimedia.org/wikipedia/commons/6/6e/France_map_blank.svg";

    // Overlay discret (ne bloque pas les clics)
    L.imageOverlay(imageUrl, FR_BOUNDS, {
      opacity: 0.16,
      interactive: false,
      crossOrigin: true
    }).addTo(map);
  }
  addMapImageOverlay();

  // -----------------------------
  // STYLES GEOJSON (hover + sélection)
  // -----------------------------
  function baseStyle() {
    return {
      color: "rgba(15, 23, 42, 0.55)",
      weight: 1,
      fillColor: "#7aa7ff",
      fillOpacity: 0.45
    };
  }
  function hoverStyle() {
    return {
      weight: 2,
      color: "rgba(2, 132, 199, 0.9)",
      fillOpacity: 0.65
    };
  }
  function selectedStyle() {
    return {
      weight: 2.5,
      color: "rgba(30, 64, 175, 0.95)",
      fillColor: "#2563eb",
      fillOpacity: 0.75
    };
  }

  let selectedLayer = null;

  // -----------------------------
  // PANEL LATÉRAL + BACKDROP
  // -----------------------------
  const panel = document.getElementById('side-panel');
  const content = document.getElementById('panel-content');
  const closeBtn = document.getElementById('close-panel');
  const backdrop = document.getElementById('panel-backdrop');

  closeBtn.addEventListener('click', closePanel);
  backdrop.addEventListener('click', closePanel);

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && panel.classList.contains('open')) {
      closePanel();
    }
  });

  function openPanel() {
    panel.classList.add('open');
    panel.setAttribute('aria-hidden', 'false');
    backdrop.classList.add('open');
    backdrop.setAttribute('aria-hidden', 'false');
  }

  function closePanel() {
    panel.classList.remove('open');
    panel.setAttribute('aria-hidden', 'true');
    backdrop.classList.remove('open');
    backdrop.setAttribute('aria-hidden', 'true');
  }

  function showPanel(html) {
    content.innerHTML = html;
    openPanel();
  }

  // -----------------------------
  // CHARGEMENT DU GEOJSON + INTERACTIONS
  // -----------------------------
  $.getJSON('data/departements.geojson', function (geojson) {

    L.geoJSON(geojson, {
      style: baseStyle,

      onEachFeature: function (feature, layer) {

        layer.on('mouseover', function () {
          if (selectedLayer !== layer) layer.setStyle(hoverStyle());
        });

        layer.on('mouseout', function () {
          if (selectedLayer !== layer) layer.setStyle(baseStyle());
        });

        layer.on('click', function () {

          // sélection visuelle
          if (selectedLayer) selectedLayer.setStyle(baseStyle());
          selectedLayer = layer;
          selectedLayer.setStyle(selectedStyle());

          // loader dans le panel
          showPanel(`
            <div class="panel-loading">
              <div class="spinner"></div>
              <p>Chargement des données…</p>
            </div>
          `);

          // conversion code dep
          let code_geo = feature.properties.code;
          let code_dep;

          if (code_geo === "2A") code_dep = 98;
          else if (code_geo === "2B") code_dep = 99;
          else code_dep = parseInt(code_geo, 10);

          $.ajax({
            url: 'get_info.php',
            method: 'POST',
            data: { code_dep: code_dep },
            success: function (data) {
              showPanel(data);
            },
            error: function (err) {
              console.log("Erreur AJAX :", err);
              showPanel("<p>Erreur lors de la récupération des données.</p>");
            }
          });

        });
      }

    }).addTo(map);

  });
</script>

<footer>
  <p>&copy; 2025 Départ(ement). Tous droits réservés. | Projet réalisé par des étudiantes de l'Université Paul Valéry</p>
</footer>

</body>
</html>
