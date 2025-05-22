<?php
// Paramètres de connexion à la base de données
$db_host = 'interchange.proxy.rlwy.net';
$db_port = '45400';
$db_user = 'root';
$db_pass = 'CdyQxqClxDFdMGfxvjvyAmlEHnbxZGqW';
$db_name = 'railway';

try {
    $pdo = new PDO(
        "mysql:host=" . $db_host . ";port=" . $db_port . ";dbname=" . $db_name . ";charset=utf8",
        $db_user,
        $db_pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch(PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
?> 