<?php
session_start();
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = filter_input(INPUT_POST, 'nom', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    $errors = [];

    // Validation
    if (empty($nom)) {
        $errors[] = "Le nom est requis";
    }

    if (empty($email)) {
        $errors[] = "L'email est requis";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "L'email n'est pas valide";
    }

    if (empty($password)) {
        $errors[] = "Le mot de passe est requis";
    } elseif (strlen($password) < 8) {
        $errors[] = "Le mot de passe doit contenir au moins 8 caractères";
    }

    if ($password !== $confirm_password) {
        $errors[] = "Les mots de passe ne correspondent pas";
    }

    // Vérifier si l'email existe déjà
    $stmt = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->rowCount() > 0) {
        $errors[] = "Cet email est déjà utilisé";
    }

    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO utilisateurs (nom, email, mot_de_passe) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nom, $email, $hashed_password]);

        if ($stmt->rowCount() > 0) {
            $_SESSION['success'] = "Inscription réussie ! Vous pouvez maintenant vous connecter.";
            header('Location: connexion.php');
            exit();
        } else {
            $errors[] = "Une erreur est survenue lors de l'inscription";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - Ohmyfood</title>
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
                <a href="connexion.php">Connexion</a>
            </div>
        </nav>
    </header>

    <main class="container">
        <h1 class="main-title">Inscription</h1>
        
        <?php if (!empty($errors)): ?>
            <div class="error">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo $error; ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="" class="form card">
            <div class="form-group">
                <label for="nom">Nom :</label>
                <input type="text" id="nom" name="nom" value="<?php echo isset($_POST['nom']) ? htmlspecialchars($_POST['nom']) : ''; ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Email :</label>
                <input type="email" id="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
            </div>

            <div class="form-group">
                <label for="password">Mot de passe :</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirmer le mot de passe :</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>

            <button type="submit" class="btn btn-primary">S'inscrire</button>
        </form>

        <p class="text-center mt-2">Déjà inscrit ? <a href="connexion.php">Connectez-vous</a></p>
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