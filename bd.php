<?php
function getBD(){
    try {
        // Nome database senza parentesi (più sicuro)
        $pdo = new PDO('mysql:host=localhost;dbname=depart_ement;charset=utf8', 'root', '');
        
        // Imposta la modalità di errore
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Imposta il modo di fetch predefinito
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        
        return $pdo;
        
    } catch(PDOException $e) {
        // Messaggio di errore dettagliato per debug
        die("
        <html>
        <head>
            <meta charset='UTF-8'>
            <title>Erreur de connexion</title>
            <style>
                body { font-family: 'Segoe UI', Arial, sans-serif; background: #f8fafc; padding: 20px; }
                .error-box { 
                    max-width: 800px; 
                    margin: 40px auto; 
                    background: #fee; 
                    border-left: 6px solid #c00; 
                    border-radius: 8px; 
                    padding: 30px;
                    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
                }
                h2 { color: #c00; margin-top: 0; }
                .message { background: white; padding: 15px; border-radius: 6px; margin: 20px 0; font-family: monospace; }
                .checklist { background: white; padding: 20px; border-radius: 6px; }
                .checklist li { margin: 10px 0; }
                .btn { 
                    display: inline-block; 
                    background: #0077b6; 
                    color: white; 
                    padding: 12px 24px; 
                    text-decoration: none; 
                    border-radius: 6px;
                    margin-top: 15px;
                }
                .btn:hover { background: #005f8e; }
            </style>
        </head>
        <body>
            <div class='error-box'>
                <h2>❌ Erreur de connexion à la base de données</h2>
                <div class='message'>
                    <strong>Message d'erreur:</strong><br>
                    " . htmlspecialchars($e->getMessage()) . "
                </div>
                
                <h3>🔍 Vérifications à faire:</h3>
                <div class='checklist'>
                    <ol>
                        <li>✅ WAMP est-il démarré? (icône verte dans la barre des tâches)</li>
                        <li>✅ MySQL est-il en cours d'exécution?</li>
                        <li>✅ La base de données <strong>'departement'</strong> existe-t-elle dans phpMyAdmin?</li>
                        <li>✅ Les tables ont-elles été importées depuis script.sql?</li>
                        <li>✅ Le nom du database dans bd.php est-il correct? (actuellement: <strong>departement</strong>)</li>
                    </ol>
                </div>
                
                <a href='http://localhost/phpmyadmin' target='_blank' class='btn'>📊 Ouvrir phpMyAdmin</a>
                <a href='javascript:history.back()' class='btn' style='background: #64748b;'>← Retour</a>
            </div>
        </body>
        </html>
        ");
    }
}
?>