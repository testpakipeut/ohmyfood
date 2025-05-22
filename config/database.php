<?php
// Paramètres de connexion à la base de données
$db_host = getenv('RAILWAY_MYSQL_HOST') ?: getenv('MYSQL_HOST') ?: 'localhost';
$db_user = getenv('RAILWAY_MYSQL_USER') ?: getenv('MYSQL_USER') ?: 'root';
$db_pass = getenv('RAILWAY_MYSQL_PASSWORD') ?: getenv('MYSQL_PASSWORD') ?: '';
$db_name = getenv('RAILWAY_MYSQL_DATABASE') ?: getenv('MYSQL_DATABASE') ?: 'ohmyfood';

// Log des paramètres de connexion (sans le mot de passe)
error_log("Tentative de connexion à la base de données :");
error_log("Host: " . $db_host);
error_log("Database: " . $db_name);
error_log("User: " . $db_user);

try {
    $pdo = new PDO(
        "mysql:host=" . $db_host . ";dbname=" . $db_name . ";charset=utf8",
        $db_user,
        $db_pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
    error_log("Connexion à la base de données réussie !");
} catch(PDOException $e) {
    error_log("Erreur de connexion à la base de données : " . $e->getMessage());
    die("Erreur de connexion à la base de données. Veuillez réessayer plus tard.");
}
?> 