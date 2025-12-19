<?php

require_once __DIR__ . '/bd.php';


if (!isset($_POST['code_dep'])) {
    echo "<p>Code département absent.</p>";
    exit;
}

$code_dep = $_POST['code_dep'];

// Sécurité : on n'accepte qu'un entier
if (!preg_match('/^\d+$/', $code_dep)) {
    echo "<p>Code département invalide.</p>";
    exit;
}

$code_dep = (int)$code_dep;

try {
    // Connexion multi-config
    $conn = getBD();

    /* -------------------------
       TABLE : departement
    ------------------------- */
    $dep = $conn->prepare("SELECT * FROM departement WHERE code_dep = ?");
    $dep->execute([$code_dep]);
    $d = $dep->fetch(PDO::FETCH_ASSOC);

    if (!$d) {
        echo "<p>Aucune donnée trouvée pour le département $code_dep.</p>";
        exit;
    }

    /* -------------------------
       TABLE : region
    ------------------------- */
    $reg = $conn->prepare("SELECT nom_region FROM region WHERE code_region = ?");
    $reg->execute([$d['code_region']]);
    $region_nom = $reg->fetchColumn();

    /* -------------------------
       TABLE : logement
    ------------------------- */
    $log = $conn->prepare("SELECT * FROM logement WHERE code_dep = ?");
    $log->execute([$code_dep]);
    $l = $log->fetch(PDO::FETCH_ASSOC);

    /* -------------------------
       TABLE : etablissement (culturels)
    ------------------------- */
    $eta = $conn->prepare("SELECT * FROM etablissement WHERE code_dep = ?");
    $eta->execute([$code_dep]);
    $e = $eta->fetch(PDO::FETCH_ASSOC);

    /* -------------------------
       SALAIRE MOYEN
    ------------------------- */
    $salaire_moyen = null;
    if (
        !empty($d['montant_salarie']) &&
        !empty($d['nbr_foyer_salarie']) &&
        (int)$d['nbr_foyer_salarie'] > 0
    ) {
        $salaire_moyen = $d['montant_salarie'] / $d['nbr_foyer_salarie'];
    }

    
    $sql_eta_sup = "
        SELECT 
            `type d'etablissement` AS type,
            COUNT(*) AS nb
        FROM eta_superieur
        WHERE code_dep = $code_dep
        GROUP BY `type d'etablissement`
        ORDER BY nb DESC
    ";

    $eta_sup_stmt = $conn->query($sql_eta_sup);
    $eta_list = $eta_sup_stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    echo "<p>Erreur : " . htmlspecialchars($e->getMessage()) . "</p>";
    exit;
}
?>

<!-- ========================
     CONTENU HTML RENVOYÉ AU PANEL
     ======================== -->

<h2><?= htmlspecialchars($d['nom_dep']) ?></h2>

<p><strong>📍 Région :</strong> <?= htmlspecialchars($region_nom ?: "—") ?></p>
<p><strong>🕵️ Population :</strong> <?= number_format($d['nbr_hab'], 0, ',', ' ') ?> habitants</p>
<p><strong>🏙️ Densité :</strong> <?= number_format($d['densite'], 0, ',', ' ') ?> hab/km²</p>
<p><strong>📉 Taux de chômage :</strong> <?= htmlspecialchars($d['taux_chomage']) ?>%</p>
<p><strong>📊 Taux de pauvreté :</strong> <?= htmlspecialchars($d['taux_pauvrete']) ?>%</p>

<?php if ($salaire_moyen !== null): ?>
<p><strong>💰 Salaire moyen :</strong>
   <?= number_format($salaire_moyen, 0, ',', ' ') ?> € / foyer salarié / an
</p>
<?php endif; ?>

<?php if ($l): ?>
<p><strong>🏠 Logements :</strong>
   <?= number_format($l['nbr_log'], 0, ',', ' ') ?> logements  
   (sociaux : <?= htmlspecialchars($l['taux_log_sociaux']) ?>%,
    individuels : <?= htmlspecialchars($l['taux_log_ind']) ?>%)
</p>
<?php endif; ?>

<?php if ($e): ?>
<p><strong>🎭 Établissements culturels :</strong>
   <?= number_format($e['nbr_t_eta'], 0, ',', ' ') ?> total
   (<?= number_format($e['nbr_eta_2018'], 0, ',', ' ') ?> en 2018)
</p>
<?php endif; ?>

<?php if (!empty($eta_list)): ?>
<p><strong>🎓 Enseignement supérieur :</strong></p>
<ul>
    <?php foreach ($eta_list as $et): ?>
        <li>— <?= (int)$et['nb'] ?> <?= htmlspecialchars($et['type']) ?></li>
    <?php endforeach; ?>
</ul>
<?php else: ?>
<p><strong>🎓 Enseignement supérieur :</strong> —</p>
<?php endif; ?>
