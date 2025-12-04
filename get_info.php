<?php
if (!isset($_POST['code_dep'])) {
    echo "<p>Code département absent.</p>";
    exit;
}

$code_dep = $_POST['code_dep'];

try {
    $conn = new PDO('mysql:host=localhost;dbname=depart_ement;charset=utf8', 'root', 'root');
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

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
    if (!empty($d['montant_salarie']) && !empty($d['nbr_foyer_salarie']) && $d['nbr_foyer_salarie'] > 0) {
        $salaire_moyen = $d['montant_salarie'] / $d['nbr_foyer_salarie'];
    }

    /* -------------------------------------------------------
       ENSEIGNEMENT SUPÉRIEUR – Comptage par type
       TABLE : eta_superieur
       Champ : `type d'etablissement`
    -------------------------------------------------------- */
    $code_dep_int = (int)$code_dep;

    $sql_eta_sup = "
        SELECT 
            `type d'etablissement` AS type,
            COUNT(*) AS nb
        FROM eta_superieur
        WHERE code_dep = $code_dep_int
        GROUP BY `type d'etablissement`
        ORDER BY nb DESC
    ";

    $eta_sup_stmt = $conn->query($sql_eta_sup);
    $eta_list = $eta_sup_stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    echo "<p>Erreur : " . $e->getMessage() . "</p>";
    exit;
}
?>

<h2><?= $d['nom_dep'] ?></h2>

<p><strong>📍 Région :</strong> <?= $region_nom ?></p>
<p><strong>🕵️ Population :</strong> <?= $d['nbr_hab'] ?> habitants</p>
<p><strong>🏙️ Densité :</strong> <?= $d['densite'] ?> hab/km²</p>
<p><strong>📉 Taux de chômage :</strong> <?= $d['taux_chomage'] ?>%</p>
<p><strong>📊 Taux de pauvreté :</strong> <?= $d['taux_pauvrete'] ?>%</p>

<?php if ($salaire_moyen !== null): ?>
<p><strong>💰 Salaire moyen :</strong> 
   <?= number_format($salaire_moyen, 0, ',', ' ') ?> € / foyer salarié / an
</p>
<?php endif; ?>

<?php if ($l): ?>
<p><strong>🏠 Logements :</strong>
   <?= $l['nbr_log'] ?> logements  
   (sociaux : <?= $l['taux_log_sociaux'] ?>%, individuels : <?= $l['taux_log_ind'] ?>%)
</p>
<?php endif; ?>

<?php if ($e): ?>
<p><strong>🎭 Établissements culturels :</strong>
   <?= $e['nbr_t_eta'] ?> total
   (<?= $e['nbr_eta_2018'] ?> en 2018)
</p>
<?php endif; ?>

<?php if (!empty($eta_list)): ?>
    <p><strong>🎓 Enseignement supérieur :</strong></p>
    <ul>
        <?php foreach ($eta_list as $et): ?>
            <li>— <?= $et['nb'] ?> <?= $et['type'] ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>
