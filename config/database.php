<?php
// Paramètres de connexion à la base de données
$db_host = 'containers-us-west-207.railway.app';
$db_user = 'root';
$db_pass = 'YOUR_PASSWORD'; // Remplacez par le mot de passe de votre base Railway
$db_name = 'railway';

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