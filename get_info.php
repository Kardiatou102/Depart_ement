<?php
// Debug pour voir les erreurs
ini_set("display_errors", 1);
error_reporting(E_ALL);

require_once "bd.php";
$conn = getBD();

if (!isset($_POST['code_dep'])) {
    echo "<p>Code département manquant.</p>";
    exit;
}

$code_dep = $_POST['code_dep'];

try {

    // --- TABLE departement ---
    $q = $conn->prepare("SELECT * FROM departement WHERE code_dep = ?");
    $q->execute([$code_dep]);
    $d = $q->fetch();

    if (!$d) {
        echo "<p>Aucune donnée trouvée pour ce département.</p>";
        exit;
    }

    // --- TABLE logement ---
    $q = $conn->prepare("SELECT * FROM logement WHERE code_dep = ?");
    $q->execute([$code_dep]);
    $l = $q->fetch();

    // --- TABLE etablissement ---
    $q = $conn->prepare("SELECT * FROM etablissement WHERE code_dep = ?");
    $q->execute([$code_dep]);
    $e = $q->fetch();

    // --- TABLE eta_superieur ---
    $q = $conn->prepare("SELECT COUNT(*) FROM eta_superieur WHERE code_dep = ?");
    $q->execute([$code_dep]);
    $nbr_sup = $q->fetchColumn();

} catch (PDOException $ex) {
    echo "<p>Erreur SQL : " . $ex->getMessage() . "</p>";
    exit;
}
?>

<h2><?= htmlspecialchars($d['nom_dep']) ?></h2>

<p><strong>Population :</strong> <?= $d['nbr_hab'] ?> habitants</p>
<p><strong>Densité :</strong> <?= $d['densite'] ?> hab/km²</p>
<p><strong>Taux de chômage :</strong> <?= $d['taux_chomage'] ?>%</p>
<p><strong>Taux de pauvreté :</strong> <?= $d['taux_pauvrete'] ?>%</p>

<?php if ($l): ?>
    <p><strong>Logements :</strong>  
        <?= $l['nbr_log'] ?> logements  
        (sociaux : <?= $l['taux_log_sociaux'] ?>%,  
         individuels : <?= $l['taux_log_ind'] ?>%)
    </p>
<?php endif; ?>

<?php if ($e): ?>
    <p><strong>Établissements culturels :</strong>
        <?= $e['nbr_t_eta'] ?>  
        (dont <?= $e['nbr_eta_2018'] ?> en 2018)
    </p>
<?php endif; ?>

<p><strong>Établissements supérieurs :</strong> <?= $nbr_sup ?></p>
