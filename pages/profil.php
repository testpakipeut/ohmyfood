<?php
$pageTitle = "Mon Profil";
require_once '../includes/header.php';
require_once '../config/database.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

$success = '';
$error = '';

// Récupérer les informations de l'utilisateur
$stmt = $pdo->prepare("SELECT name, email, created_at FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Traitement du formulaire de mise à jour
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $new_password_confirm = $_POST['new_password_confirm'] ?? '';

    if (empty($name) || empty($email)) {
        $error = "Le nom et l'email sont obligatoires";
    } else {
        // Vérifier si l'email est déjà utilisé par un autre utilisateur
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->execute([$email, $_SESSION['user_id']]);
        if ($stmt->rowCount() > 0) {
            $error = "Cet email est déjà utilisé par un autre compte";
        } else {
            // Mettre à jour les informations de base
            $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
            $stmt->execute([$name, $email, $_SESSION['user_id']]);
            
            if ($stmt->rowCount() > 0) {
                $_SESSION['user_name'] = $name;
                $success = "Profil mis à jour avec succès";
                
                // Mettre à jour l'affichage
                $user['name'] = $name;
                $user['email'] = $email;
            } else {
                $error = "Erreur lors de la mise à jour du profil";
            }
        }
    }

    // Changer le mot de passe si demandé
    if (!empty($current_password) && !empty($new_password)) {
        if ($new_password !== $new_password_confirm) {
            $error = "Les nouveaux mots de passe ne correspondent pas";
        } elseif (strlen($new_password) < 8) {
            $error = "Le nouveau mot de passe doit contenir au moins 8 caractères";
        } else {
            // Vérifier l'ancien mot de passe
            $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $result = $stmt->fetch();
            
            if (password_verify($current_password, $result['password'])) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->execute([$hashed_password, $_SESSION['user_id']]);
                
                $success = "Mot de passe mis à jour avec succès";
            } else {
                $error = "Mot de passe actuel incorrect";
            }
        }
    }
}
?>

<div class="container">
    <div class="profile-container">
        <h1>Mon Profil</h1>
        
        <?php if ($success): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-error">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <div class="profile-section">
            <h2>Informations personnelles</h2>
            <form method="post" class="form">
                <div class="form-group">
                    <label for="name">Nom complet</label>
                    <input type="text" id="name" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                </div>
                
                <button type="submit" class="btn btn-primary">Mettre à jour</button>
            </form>
        </div>

        <div class="profile-section">
            <h2>Changer le mot de passe</h2>
            <form method="post" class="form">
                <div class="form-group">
                    <label for="current_password">Mot de passe actuel</label>
                    <input type="password" id="current_password" name="current_password">
                </div>
                
                <div class="form-group">
                    <label for="new_password">Nouveau mot de passe</label>
                    <input type="password" id="new_password" name="new_password" 
                           minlength="8" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}"
                           title="Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule et un chiffre">
                </div>
                
                <div class="form-group">
                    <label for="new_password_confirm">Confirmer le nouveau mot de passe</label>
                    <input type="password" id="new_password_confirm" name="new_password_confirm">
                </div>
                
                <button type="submit" class="btn btn-primary">Changer le mot de passe</button>
            </form>
        </div>

        <div class="profile-section">
            <h2>Informations du compte</h2>
            <p>Membre depuis : <?= date('d/m/Y', strtotime($user['created_at'])) ?></p>
        </div>
    </div>
</div>

