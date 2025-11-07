<?php
require_once("bd.php");

// Connessione al database
$pdo = getBD();

// Gestione della ricerca
$resultats = [];
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $conditions = [];
    $params = [];

    if (!empty($_POST["nom_dep"])) {
        $conditions[] = "nom_dep LIKE :nom_dep";
        $params[":nom_dep"] = "%" . $_POST["nom_dep"] . "%";
    }

    if (!empty($_POST["code_region"])) {
        $conditions[] = "code_region = :code_region";
        $params[":code_region"] = $_POST["code_region"];
    }

    if (!empty($_POST["densite_min"])) {
        $conditions[] = "densite >= :densite_min";
        $params[":densite_min"] = $_POST["densite_min"];
    }

    if (!empty($_POST["taux_chomage_max"])) {
        $conditions[] = "taux_chomage <= :taux_chomage_max";
        $params[":taux_chomage_max"] = $_POST["taux_chomage_max"];
    }

    $sql = "SELECT d.*, r.nom_region 
            FROM departement d 
            LEFT JOIN region r ON d.code_region = r.code_region";

    if (count($conditions) > 0) {
        $sql .= " WHERE " . implode(" AND ", $conditions);
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $resultats = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Recherche de Départements</title>
    <link rel="stylesheet" href="style1.css">
</head>
<body>
    <h1>Recherche par critères</h1>

    <form method="post">
        <label>Nom du département :</label>
        <input type="text" name="nom_dep">

        <label>Code région :</label>
        <input type="number" name="code_region">

        <label>Densité minimale :</label>
        <input type="number" step="0.1" name="densite_min">

        <label>Taux de chômage maximal :</label>
        <input type="number" step="0.1" name="taux_chomage_max">

        <button type="submit">Rechercher</button>
    </form>

    <?php if ($_SERVER["REQUEST_METHOD"] === "POST") : ?>
        <h2>Résultats :</h2>
        <?php if (count($resultats) > 0): ?>
            <table border="1">
                <tr>
                    <th>Code</th>
                    <th>Nom</th>
                    <th>Région</th>
                    <th>Densité</th>
                    <th>Taux chômage</th>
                    <th>Population</th>
                </tr>
                <?php foreach ($resultats as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row["code_dep"]) ?></td>
                    <td><?= htmlspecialchars($row["nom_dep"]) ?></td>
                    <td><?= htmlspecialchars($row["nom_region"]) ?></td>
                    <td><?= htmlspecialchars($row["densite"]) ?></td>
                    <td><?= htmlspecialchars($row["taux_chomage"]) ?></td>
                    <td><?= htmlspecialchars($row["nbr_hab"]) ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>Aucun résultat trouvé.</p>
        <?php endif; ?>
    <?php endif; ?>
</body>
</html>
