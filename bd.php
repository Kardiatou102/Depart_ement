<?php


function getBD() {

    $dbname = "depart_ement";

    // Liste des configs possibles
    $configs = [
        // MAMP par défaut (port 8889)
        ["host" => "localhost", "port" => "8889", "user" => "root", "pass" => "root"],

        // MAMP MySQL normal
        ["host" => "localhost", "port" => "3306", "user" => "root", "pass" => "root"],

        // WAMP (root sans mot de passe)
        ["host" => "localhost", "port" => "3306", "user" => "root", "pass" => ""],

        // WAMP (root/root)
        ["host" => "localhost", "port" => "3306", "user" => "root", "pass" => "root"],

        // XAMPP
        ["host" => "localhost", "port" => "3306", "user" => "root", "pass" => ""]
    ];

    foreach ($configs as $c) {
        try {
            $pdo = new PDO(
                "mysql:host={$c['host']};port={$c['port']};dbname=$dbname;charset=utf8",
                $c['user'],
                $c['pass'],
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );
            return $pdo; // connexion OK → on renvoie directement
        } catch (PDOException $e) {
            // On continue simplement avec la config suivante
        }
    }

    // Si AUCUNE config ne fonctionne :
    die("Erreur : impossible de se connecter à la base de données.");
}
?>
