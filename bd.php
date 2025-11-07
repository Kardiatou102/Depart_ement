<?php
function getBD(){
    try {
	    $bdd = new PDO('mysql:host=localhost;dbname=depart(ement);charset=utf8', 'root', '');

        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
        
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        
        return $pdo;
        
    } catch(PDOException $e) {
        die("Errore di connessione: " . $e->getMessage());
    }
}
?>