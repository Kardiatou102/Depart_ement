<?php
function getBD(){
    // Configurazioni per diversi ambienti
    $configs = [
        // MAMP (macOS) - prova prima questo
        [
            'host' => 'localhost',
            'port' => '8889', // Porta predefinita MAMP
            'user' => 'root',
            'pass' => 'root',
            'name' => 'MAMP'
        ],
        // MAMP alternativo (porta MySQL standard)
        [
            'host' => 'localhost',
            'port' => '3306',
            'user' => 'root',
            'pass' => 'root',
            'name' => 'MAMP (porta standard)'
        ],
        // WAMP (Windows)
        [
            'host' => 'localhost',
            'port' => '3306',
            'user' => 'root',
            'pass' => '', // WAMP spesso ha password vuota
            'name' => 'WAMP (senza password)'
        ],
        // WAMP con password
        [
            'host' => 'localhost',
            'port' => '3306',
            'user' => 'root',
            'pass' => 'root',
            'name' => 'WAMP (con password)'
        ],
        // XAMPP
        [
            'host' => 'localhost',
            'port' => '3306',
            'user' => 'root',
            'pass' => '',
            'name' => 'XAMPP'
        ]
    ];

    $dbname = 'depart_ement'; // Cambia questo con il nome del tuo database
    $lastError = null;
    $attemptedConfigs = [];

    // Prova ogni configurazione
    foreach ($configs as $config) {
        try {
            $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$dbname};charset=utf8";
            
            $pdo = new PDO($dsn, $config['user'], $config['pass']);
            
            // Imposta la modalità di errore
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Imposta il modo di fetch predefinito
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
            // ✅ Connessione riuscita!
            // Opzionale: decommentare per vedere quale config ha funzionato
            // echo "<!-- Connesso con: {$config['name']} -->";
            
            return $pdo;
            
        } catch(PDOException $e) {
            // Salva l'errore e continua a provare
            $lastError = $e->getMessage();
            $attemptedConfigs[] = $config['name'];
        }
    }
    
    // Se arriviamo qui, nessuna configurazione ha funzionato
    showDetailedError($lastError, $attemptedConfigs, $dbname);
}

function showDetailedError($errorMessage, $attemptedConfigs, $dbname) {
    // Rileva il sistema operativo probabile
    $os = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' ? 'Windows (WAMP?)' : 'macOS/Linux (MAMP?)';
    
    die("
    <html>
    <head>
        <meta charset='UTF-8'>
        <title>Errore di connessione al database</title>
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body { 
                font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, Arial, sans-serif; 
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                padding: 20px;
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .container {
                max-width: 900px;
                width: 100%;
            }
            .error-box { 
                background: white;
                border-radius: 16px; 
                padding: 40px;
                box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            }
            .header {
                display: flex;
                align-items: center;
                gap: 15px;
                margin-bottom: 30px;
                padding-bottom: 20px;
                border-bottom: 3px solid #f0f0f0;
            }
            .icon {
                font-size: 48px;
            }
            h1 { 
                color: #d32f2f; 
                font-size: 28px;
                font-weight: 600;
            }
            .os-info {
                display: inline-block;
                background: #e3f2fd;
                color: #1976d2;
                padding: 8px 16px;
                border-radius: 20px;
                font-size: 14px;
                font-weight: 500;
                margin-bottom: 20px;
            }
            .section {
                background: #f8f9fa;
                padding: 20px;
                border-radius: 10px;
                margin: 20px 0;
            }
            .section h3 {
                color: #333;
                margin-bottom: 15px;
                font-size: 18px;
            }
            .error-message {
                background: #ffebee;
                border-left: 4px solid #d32f2f;
                padding: 15px;
                border-radius: 6px;
                font-family: 'Courier New', monospace;
                font-size: 13px;
                color: #c62828;
                overflow-x: auto;
            }
            .checklist {
                list-style: none;
            }
            .checklist li {
                padding: 12px;
                margin: 8px 0;
                background: white;
                border-radius: 8px;
                border-left: 4px solid #9c27b0;
                font-size: 15px;
            }
            .checklist li strong {
                color: #6a1b9a;
            }
            .attempted {
                display: flex;
                flex-wrap: wrap;
                gap: 10px;
                margin-top: 10px;
            }
            .badge {
                background: #fff3e0;
                color: #e65100;
                padding: 6px 12px;
                border-radius: 6px;
                font-size: 13px;
                font-weight: 500;
            }
            .buttons {
                display: flex;
                gap: 15px;
                margin-top: 30px;
                flex-wrap: wrap;
            }
            .btn { 
                display: inline-flex;
                align-items: center;
                gap: 8px;
                background: #667eea;
                color: white;
                padding: 14px 24px;
                text-decoration: none;
                border-radius: 8px;
                font-weight: 500;
                transition: all 0.3s;
                box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
            }
            .btn:hover { 
                background: #5568d3;
                transform: translateY(-2px);
                box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
            }
            .btn-secondary {
                background: #64748b;
                box-shadow: 0 4px 12px rgba(100, 116, 139, 0.3);
            }
            .btn-secondary:hover {
                background: #475569;
            }
            .code {
                background: #263238;
                color: #aed581;
                padding: 3px 8px;
                border-radius: 4px;
                font-family: 'Courier New', monospace;
                font-size: 13px;
            }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='error-box'>
                <div class='header'>
                    <span class='icon'></span>
                    <div>
                        <h1>Impossibile connettersi al database</h1>
                        <div class='os-info'>Sistema rilevato: $os</div>
                    </div>
                </div>
                
                <div class='section'>
                    <h3>Configurazioni tentate</h3>
                    <div class='attempted'>" . 
                        implode('', array_map(function($config) {
                            return "<span class='badge'> $config</span>";
                        }, $attemptedConfigs)) . "
                    </div>
                </div>
                
                <div class='section'>
                    <h3>Ultimo messaggio di errore</h3>
                    <div class='error-message'>" . htmlspecialchars($errorMessage) . "</div>
                </div>
                
                <div class='section'>
                    <h3>Checklist di risoluzione</h3>
                    <ul class='checklist'>
                        <li><strong>1.</strong> Il server è avviato? Controlla che WAMP/MAMP abbia l'icona <strong>verde</strong></li>
                        <li><strong>2.</strong> MySQL è in esecuzione? Verifica nei servizi del tuo server</li>
                        <li><strong>3.</strong> Il database <span class='code'>$dbname</span> esiste in phpMyAdmin?</li>
                        <li><strong>4.</strong> Se usi <strong>MAMP</strong>, la porta è 8889 o 3306? Controlla in Preferenze → Ports</li>
                        <li><strong>5.</strong> Se usi <strong>WAMP</strong>, la password di root potrebbe essere vuota o 'root'</li>
                        <li><strong>6.</strong> Hai importato il file SQL con le tabelle?</li>
                    </ul>
                </div>
                
                <div class='buttons'>
                    <a href='http://localhost/phpmyadmin' target='_blank' class='btn'>
                        Apri phpMyAdmin
                    </a>
                    <a href='http://localhost:8888/phpMyAdmin/' target='_blank' class='btn'>
                        phpMyAdmin MAMP
                    </a>
                    <a href='javascript:location.reload()' class='btn btn-secondary'>
                        Riprova
                    </a>
                </div>
            </div>
        </div>
    </body>
    </html>
    ");
}
?>