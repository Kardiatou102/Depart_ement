<?php
require_once 'bd.php';

$resultats = [];
$critere_recherche = '';
$recherche_effectuee = false;
$ordre = 'DESC'; // Par défaut: du plus élevé au plus bas

// Liste des critères disponibles avec icônes et descriptions
$criteres_disponibles = [
    'taux_chomage' => [
        'label' => 'Taux de chômage',
        'icon' => '💼',
        'description' => 'Taux de chômage le plus bas',
        'unite' => '%',
        'inverse' => true // true = on préfère les valeurs basses
    ],
    'taux_pauvrete' => [
        'label' => 'Taux de pauvreté',
        'icon' => '💰',
        'description' => 'Taux de pauvreté le plus bas',
        'unite' => '%',
        'inverse' => true
    ],
    'densite' => [
        'label' => 'Densité de population',
        'icon' => '👥',
        'description' => 'Densité de population',
        'unite' => ' hab/km²',
        'inverse' => false
    ],
    'pourcpopvingt' => [
        'label' => 'Population jeune (-20 ans)',
        'icon' => '👶',
        'description' => 'Plus de jeunes',
        'unite' => '%',
        'inverse' => false
    ],
    'pourcpopsoixante' => [
        'label' => 'Population senior (+60 ans)',
        'icon' => '👴',
        'description' => 'Plus de seniors',
        'unite' => '%',
        'inverse' => false
    ],
    'taux_log_sociaux' => [
        'label' => 'Logements sociaux',
        'icon' => '🏠',
        'description' => 'Plus de logements sociaux',
        'unite' => '%',
        'inverse' => false
    ],
    'nbr_hab' => [
        'label' => 'Population totale',
        'icon' => '🌆',
        'description' => 'Départements les plus peuplés',
        'unite' => ' hab.',
        'inverse' => false
    ]
];

// Traitement de la recherche
if (!empty($_GET['critere'])) {
	if (isset($_GET['order']) && in_array($_GET['order'], ['ASC', 'DESC'])) {
	$ordre = $_GET['order'];
	}
	elseif ($criteres_disponibles[$critere_recherche]['inverse']){
		$ordre = 'ASC';
	} else {
		$ordre = 'DESC';
	}
    $recherche_effectuee = true;
    $critere_recherche = $_GET['critere'];
    

    
    try {
        $pdo = getBD();
        
        // Déterminer la colonne et les jointures nécessaires
        $colonne = '';
        $table_join = '';
        
        switch($critere_recherche) {
            case 'taux_chomage':
            case 'taux_pauvrete':
            case 'densite':
            case 'pourcpopvingt':
            case 'pourcpopsoixante':
            case 'nbr_hab':
                $colonne = "d." . $critere_recherche;
                break;
            case 'taux_log_sociaux':
                $colonne = "l.taux_log_sociaux";
                $table_join = "LEFT JOIN logement l ON d.code_dep = l.code_dep";
                break;
        }
        
        if ($colonne) {
            $sql = "SELECT d.code_dep, d.nom_dep, r.nom_region, $colonne as valeur,
                           d.nbr_hab, d.densite, d.taux_chomage, d.taux_pauvrete
                    FROM departement d
                    LEFT JOIN region r ON d.code_region = r.code_region
                    $table_join
                    WHERE $colonne IS NOT NULL
                    ORDER BY valeur $ordre
                    LIMIT 30";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $resultats = $stmt->fetchAll();
        }
        
    } catch(PDOException $e) {
        $erreur = "Erreur : " . $e->getMessage();
    }
}

// Récupérer les régions pour le filtre
try {
    $pdo = getBD();
    $stmt = $pdo->query("SELECT code_region, nom_region FROM region ORDER BY nom_region");
    $regions = $stmt->fetchAll();
} catch(PDOException $e) {
    $regions = [];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Choisir ses critères - Départ(ement)</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Header -->
    <header>
        <nav>
        <h1 class="logo">Départ(ement)</h1>
        <ul>
            <li class="active">Accueil </li>
            <li><a href="carte.php">Carte</a></li>
            <li><a href="apropos.html">À propos</a></li>
            <li><a href="contact.html">Contact</a></li>
        </ul>
        </nav>
    </header>

    <!-- Section principale -->
    <main class="criteres-main">
        <div class="criteres-hero">
            <div class="overlay"></div>
            <div class="criteres-content">
                <h1>🔍 Trouvez votre département idéal</h1>
                <p>Sélectionnez un critère pour découvrir les meilleurs départements</p>
                
                <!-- Sélecteur de critères sous forme de cartes -->
                <div class="criteres-grid">
                    <?php foreach($criteres_disponibles as $key => $critere): ?>
                        <a href="?critere=<?= $key ?>" class="critere-card <?= ($critere_recherche === $key) ? 'active' : '' ?>">
                            <div class="critere-icon"><?= $critere['icon'] ?></div>
                            <div class="critere-title"><?= htmlspecialchars($critere['label']) ?></div>
                            <div class="critere-desc"><?= htmlspecialchars($critere['description']) ?></div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <?php if ($recherche_effectuee && count($resultats) > 0): ?>
            <!-- Section des résultats -->
            <section class="resultats-section">
                <div class="resultats-header">
                    <h2>
                        <?= $criteres_disponibles[$critere_recherche]['icon'] ?> 
                        Top 30 - <?= htmlspecialchars($criteres_disponibles[$critere_recherche]['label']) ?>
                    </h2>
                    <p class="resultats-subtitle">
                        <?= count($resultats) ?> départements trouvés • 
                        Triés par <form method="GET" class="tri-form">
    						<input type="hidden" name="critere" value="<?= $critere_recherche ?>">
    							<select name="order" id="order" onchange="this.form.submit()">
        							<option value="DESC" <?= ($ordre === 'DESC' ? 'selected' : '') ?>>Décroissant</option>
        							<option value="ASC"  <?= ($ordre === 'ASC'  ? 'selected' : '') ?>>Croissant</option>
    							</select>
							</form>
                    </p>
                </div>

                <!-- Affichage en mode liste avec détails -->
                <div class="resultats-liste">
                    <?php foreach($resultats as $index => $dept): ?>
                        <div class="resultat-item" style="animation-delay: <?= $index * 0.05 ?>s">
                            <div class="resultat-rank">#<?= $index + 1 ?></div>
                            <div class="resultat-info">
                                <h3><?= htmlspecialchars($dept['nom_dep']) ?> <span class="code">(<?= $dept['code_dep'] ?>)</span></h3>
                                <p class="region-name">📍 <?= htmlspecialchars($dept['nom_region']) ?></p>
                            </div>
                            <div class="resultat-stats">
                                <div class="stat-primary">
                                    <span class="stat-value-big">
                                        <?php 
                                        $valeur = $dept['valeur'];
                                        if ($critere_recherche === 'nbr_hab') {
                                            echo number_format($valeur, 0, ',', ' ');
                                        } else {
                                            echo number_format($valeur, 1, ',', ' ');
                                        }
                                        echo $criteres_disponibles[$critere_recherche]['unite'];
                                        ?>
                                    </span>
                                </div>
                                <div class="stats-secondaires">
                                    <span class="mini-stat">👥 <?= number_format($dept['nbr_hab'], 0, ',', ' ') ?> hab.</span>
                                    <span class="mini-stat">💼 <?= number_format($dept['taux_chomage'], 1) ?>% chômage</span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Bouton pour comparer -->
                <div class="actions-bottom">
                    <a href="#" class="btn btn-secondary" onclick="window.print(); return false;">📄 Imprimer les résultats</a>
                    <a href="carte.html" class="btn">🗺️ Voir sur la carte</a>
                </div>
            </section>
        <?php elseif ($recherche_effectuee): ?>
            <section class="no-results-section">
                <div class="no-results">
                    <div class="no-results-icon">😕</div>
                    <h2>Aucun résultat trouvé</h2>
                    <p>Essayez de sélectionner un autre critère</p>
                </div>
            </section>
        <?php endif; ?>
    </main>

    <footer>
        <p>&copy; 2025 Départ(ement). Tous droits réservés.</p>
    </footer>

    <script>
        // Animation au scroll
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        }, { threshold: 0.1 });

        document.querySelectorAll('.resultat-item').forEach(item => {
            observer.observe(item);
        });

        // Smooth scroll vers les résultats
        <?php if ($recherche_effectuee): ?>
        setTimeout(() => {
            const resultatsSection = document.querySelector('.resultats-section');
            if (resultatsSection) {
                resultatsSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }, 100);
        <?php endif; ?>
    </script>
</body>
</html>