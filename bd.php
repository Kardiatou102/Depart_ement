<?php

function getBD() {

    $dbname = "depart_ement";

    $configs = [
        ["host" => "localhost", "port" => "8889", "user" => "root", "pass" => "root"], // MAMP 8889
        ["host" => "localhost", "port" => "3306", "user" => "root", "pass" => "root"], // MAMP 3306
        ["host" => "localhost", "port" => "3306", "user" => "root", "pass" => ""],     // WAMP/XAMPP
        ["host" => "localhost", "port" => "3306", "user" => "root", "pass" => "root"],
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
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,

                    // ✅ CRUCIAL : force PDO à remplacer les paramètres avant envoi à MySQL
                    PDO::ATTR_EMULATE_PREPARES => true,
                ]
            );
            return $pdo;
        } catch (PDOException $e) {
            // continue
        }
    }

    die("Erreur : impossible de se connecter à la base de données.");
}
