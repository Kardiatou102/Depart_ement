<?php
if (!isset($_POST['code_dep'])) {
    echo "<p>Code département absent.</p>";
    exit;
}

$code_dep = $_POST['code_dep'];

try {
    $conn = new PDO('mysql:host=localhost;dbname=depart_ement;charset=utf8', 'root', 'root');
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // ==========================
    // TABLE : departement
    // ==========================
    $dep = $conn->prepare("SELECT * FROM departement WHERE code_dep = ?");
    $dep->execute([$code_dep]);
    $d = $dep->fetch();

    if (!$d) {
        echo "<p>Aucune donnée trouvée pour le département $code_dep.</p>";
        exit;
    }

    // ==========================
    // TABLE : logement
    // ==========================
    $log = $conn->prepare("SELECT * FROM logement WHERE code_dep = ?");
    $log->execute([$code_dep]);
    $l = $log->fetch();

    // ==========================
    // TABLE : etablissement
    // ==========================
    $eta = $conn->prepare("SELECT * FROM etablissement WHERE code_dep = ?");
    $eta->execute([$code_dep]);
    $e = $eta->fetch();

    // ==========================
    // TABLE : eta_superieur
    // ==========================
    $sup = $conn->prepare("SELECT COUNT(*) FROM eta_superieur WHERE code_dep = ?");
    $sup->execute([$code_dep]);
    $nbr_sup = $sup->fetchColumn();

} catch (Exception $e) {
    echo "<p>Erreur : " . $e->getMessage() . "</p>";
    exit;
}

?>

<h2><?= $d['nom_dep'] ?></h2>

<p><strong>Population :</strong> <?= $d['nbr_hab'] ?> habitants</p>
<p><strong>Densité :</strong> <?= $d['densite'] ?> hab/km²</p>
<p><strong>Taux de chômage :</strong> <?= $d['taux_chomage'] ?>%</p>
<p><strong>Taux de pauvreté :</strong> <?= $d['taux_pauvrete'] ?>%</p>

<?php if ($l): ?>
<p><strong>Logements :</strong> <?= $l['nbr_log'] ?>  
 (sociaux : <?= $l['taux_log_sociaux'] ?>%, individuels : <?= $l['taux_log_ind'] ?>%)</p>
<?php endif; ?>

<?php if ($e): ?>
<p><strong>Établissements culturels :</strong>  
   <?= $e['nbr_t_eta'] ?> (dont <?= $e['nbr_eta_2018'] ?> en 2018)</p>
<?php endif; ?>

<p><strong>Établissements supérieurs :</strong> <?= $nbr_sup ?></p>
