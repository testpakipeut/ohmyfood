<?php
session_start();
require_once '../config/database.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($nom) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "Veuillez remplir tous les champs";
    } elseif ($password !== $confirm_password) {
        $error = "Les mots de passe ne correspondent pas";
    } elseif (strlen($password) < 8) {
        $error = "Le mot de passe doit contenir au moins 8 caractères";
    } else {
        // Vérifier si l'email existe déjà
        $stmt = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            $error = "Cet email est déjà utilisé";
        } else {
            // Créer le compte
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom, email, mot_de_passe, role) VALUES (?, ?, ?, 'client')");
            $stmt->execute([$nom, $email, $hashed_password]);
            
            if ($stmt->rowCount() > 0) {
                $success = "Compte créé avec succès ! Vous pouvez maintenant vous connecter.";
            } else {
                $error = "Erreur lors de la création du compte";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - OhMyFood</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: '#1A1A68',
            secondary: '#F0C15C',
            'light-bg': '#F5F5F5',
            'dark-text': '#333333'
          }
        }
      }
    }
    </script>
</head>
<body class="bg-light-bg text-dark-text">
    <header class="bg-white shadow-md fixed w-full top-0 z-50">
        <nav class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
            <div class="h-full flex items-center justify-center">
                <img src="https://i.postimg.cc/4Ny6gmBd/logoohmyfood.png" class="max-h-full object-contain" alt="OhMyFood" />
            </div>
            <div class="hidden md:flex items-center space-x-8">
                <a href="../index.php" class="text-primary font-medium hover:text-secondary transition-colors">Accueil</a>
                <a href="about.php" class="text-primary font-medium hover:text-secondary transition-colors">À propos</a>
                <a href="restaurants.php" class="text-primary font-medium hover:text-secondary transition-colors">Restaurants</a>
                <a href="reservation.php" class="bg-secondary text-primary font-bold px-5 py-2 rounded-lg shadow hover:bg-primary hover:text-secondary transition-colors duration-200">Réserver</a>
                <?php if (empty($_SESSION['user_id'])): ?>
                    <a href="login.php" class="ml-4 bg-primary text-white font-bold px-5 py-2 rounded-lg shadow hover:bg-secondary hover:text-primary transition-colors duration-200">Connexion</a>
                    <a href="register.php" class="ml-2 bg-secondary text-primary font-bold px-5 py-2 rounded-lg shadow hover:bg-primary hover:text-secondary transition-colors duration-200">Inscription</a>
                <?php else: ?>
                    <a href="mes-reservations.php" class="ml-4 bg-secondary text-primary font-bold px-5 py-2 rounded-lg shadow hover:bg-primary hover:text-secondary transition-colors duration-200">Mes réservations</a>
                    <span class="ml-4 text-primary flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A9 9 0 1112 21a9 9 0 01-6.879-3.196z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        Bonjour, <?= htmlspecialchars($_SESSION['user_name']) ?>
                    </span>
                    <a href="logout.php" class="ml-2 bg-primary text-white font-bold px-5 py-2 rounded-lg shadow hover:bg-secondary hover:text-primary transition-colors duration-200">Déconnexion</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>

    <main class="pt-32 pb-12">
        <div class="max-w-md mx-auto px-4">
            <div class="bg-white rounded-xl shadow-lg p-8">
                <h1 class="text-2xl font-bold text-primary mb-6 text-center">Inscription</h1>
                
                <?php if ($error): ?>
                    <div class="bg-red-100 text-red-800 px-4 py-2 rounded mb-4">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">
                        <?= htmlspecialchars($success) ?>
                    </div>
                <?php endif; ?>

                <form method="post" class="space-y-6">
                    <div>
                        <label for="nom" class="block font-semibold mb-1">Nom</label>
                        <input type="text" id="nom" name="nom" required 
                               class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary">
                    </div>

                    <div>
                        <label for="email" class="block font-semibold mb-1">Email</label>
                        <input type="email" id="email" name="email" required 
                               class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary">
                    </div>
                    
                    <div>
                        <label for="password" class="block font-semibold mb-1">Mot de passe</label>
                        <input type="password" id="password" name="password" required 
                               class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary">
                        <p class="text-sm text-gray-500 mt-1">Minimum 8 caractères</p>
                    </div>

                    <div>
                        <label for="confirm_password" class="block font-semibold mb-1">Confirmer le mot de passe</label>
                        <input type="password" id="confirm_password" name="confirm_password" required 
                               class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary">
                    </div>

                    <button type="submit" 
                            class="w-full bg-primary text-white font-bold px-6 py-2 rounded-lg shadow hover:bg-secondary hover:text-primary transition-colors">
                        Créer un compte
                    </button>
                </form>

                <div class="mt-6 text-center">
                    <p class="text-gray-600">Déjà un compte ?</p>
                    <a href="login.php" class="text-primary font-semibold hover:text-secondary transition-colors">
                        Se connecter
                    </a>
                </div>
            </div>
        </div>
    </main>
</body>
</html> 