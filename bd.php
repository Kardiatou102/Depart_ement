<?php
function getBD(){
    try {
        $pdo = new PDO('mysql:host=localhost;dbname=depart(ement);charset=utf8', 'root', '');

        
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        
        return $pdo;
        
    } catch(PDOException $e) {
        die("Errore di connessione: " . $e->getMessage());
    }
}
?>