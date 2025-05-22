<?php
session_start();
require_once '../config/database.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: connexion.php');
    exit();
}

$restaurant_id = isset($_GET['restaurant_id']) ? (int)$_GET['restaurant_id'] : 0;

// Récupérer les informations du restaurant
$stmt = $pdo->prepare("SELECT * FROM restaurants WHERE id = ?");
$stmt->execute([$restaurant_id]);
$restaurant = $stmt->fetch();

if (!$restaurant) {
    header('Location: ../index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = $_POST['date'];
    $heure = $_POST['heure'];
    $nombre_personnes = (int)$_POST['nombre_personnes'];
    $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);

    $date_reservation = $date . ' ' . $heure;

    $sql = "INSERT INTO reservations (restaurant_id, nom, email, telephone, date_reservation, nombre_personnes, message) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $restaurant_id,
        $_SESSION['user_name'],
        $_SESSION['user_email'],
        $_POST['telephone'],
        $date_reservation,
        $nombre_personnes,
        $message
    ]);

    if ($stmt->rowCount() > 0) {
        $_SESSION['success'] = "Votre réservation a été enregistrée avec succès !";
        header('Location: mes-reservations.php');
        exit();
    } else {
        $error = "Une erreur est survenue lors de la réservation";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réservation - <?php echo htmlspecialchars($restaurant['nom']); ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <header class="header">
        <nav class="nav">
            <a href="../index.php" class="logo-link">
                <img src="../assets/images/logo.png" alt="Ohmyfood" class="logo">
            </a>
            <div class="nav-links">
                <a href="../index.php">Accueil</a>
                <a href="mes-reservations.php">Mes réservations</a>
                <a href="deconnexion.php">Déconnexion</a>
            </div>
        </nav>
    </header>

    <main class="container">
        <h1 class="main-title">Réservation - <?php echo htmlspecialchars($restaurant['nom'] ?? $restaurant['name']); ?></h1>
        
        <?php if (isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="" class="form card">
            <div class="form-group">
                <label for="date">Date :</label>
                <input type="date" id="date" name="date" required min="<?php echo date('Y-m-d'); ?>">
            </div>

            <div class="form-group">
                <label for="heure">Heure :</label>
                <input type="time" id="heure" name="heure" required>
            </div>

            <div class="form-group">
                <label for="nombre_personnes">Nombre de personnes :</label>
                <input type="number" id="nombre_personnes" name="nombre_personnes" min="1" max="20" required>
            </div>

            <div class="form-group">
                <label for="telephone">Téléphone :</label>
                <input type="tel" id="telephone" name="telephone" required>
            </div>

            <div class="form-group">
                <label for="message">Message (optionnel) :</label>
                <textarea id="message" name="message" rows="4"></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Réserver</button>
        </form>
    </main>

    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>Ohmyfood</h3>
                    <p>Votre partenaire pour des expériences gastronomiques inoubliables</p>
                </div>
                <div class="footer-section">
                    <h3>Liens utiles</h3>
                    <a href="mentions-legales.php">Mentions légales</a>
                    <a href="cgu.php">Conditions générales d'utilisation</a>
                    <a href="politique-confidentialite.php">Politique de confidentialité</a>
                </div>
                <div class="footer-section">
                    <h3>Suivez-nous</h3>
                    <div class="social-links">
                        <a href="#" aria-label="Facebook"><i class="fab fa-facebook"></i></a>
                        <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                        <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>
            <p class="copyright">&copy; 2024 Ohmyfood. Tous droits réservés.</p>
        </div>
    </footer>
</body>
</html> 