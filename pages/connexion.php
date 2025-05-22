<?php
session_start();
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    $sql = "SELECT * FROM utilisateurs WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['mot_de_passe'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['nom'];
            $_SESSION['user_role'] = $user['role'];
            header('Location: ../index.php');
            exit();
        }
    }
    $error = "Email ou mot de passe incorrect";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Ohmyfood</title>
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
                <a href="inscription.php">Inscription</a>
            </div>
        </nav>
    </header>

    <main class="container">
        <h1 class="main-title">Connexion</h1>
        
        <?php if (isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="" class="form card">
            <div class="form-group">
                <label for="email">Email :</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="password">Mot de passe :</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit" class="btn btn-primary">Se connecter</button>
        </form>

        <p class="text-center mt-2">Pas encore de compte ? <a href="inscription.php">Inscrivez-vous</a></p>
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