<?php
// Page de configuration cachée pour les utilisateurs
require_once '../config/database.php';

// Création de la table si elle n'existe pas
$conn->query("CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// Ajout d'un utilisateur
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $password);
    $stmt->execute();
}

// Suppression d'un utilisateur
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM users WHERE id = $id");
}

// Liste des utilisateurs
$result = $conn->query("SELECT id, name, email, created_at FROM users ORDER BY id DESC");
$users = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Config Utilisateurs</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>body{padding-top:40px;max-width:700px;margin:auto;}</style>
</head>
<body>
    <h1>Configuration des utilisateurs</h1>
    <form method="post" class="form card">
        <div class="form-group">
            <label>Nom</label>
            <input type="text" name="name" required>
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" required>
        </div>
        <div class="form-group">
            <label>Mot de passe</label>
            <input type="password" name="password" required>
        </div>
        <button type="submit" name="add_user" class="btn btn-primary">Ajouter</button>
    </form>
    <h2>Liste des utilisateurs</h2>
    <table border="1" cellpadding="6" style="width:100%;background:#fff;">
        <tr><th>ID</th><th>Nom</th><th>Email</th><th>Créé le</th><th>Action</th></tr>
        <?php foreach($users as $u): ?>
        <tr>
            <td><?= $u['id'] ?></td>
            <td><?= htmlspecialchars($u['name']) ?></td>
            <td><?= htmlspecialchars($u['email']) ?></td>
            <td><?= $u['created_at'] ?></td>
            <td><a href="?delete=<?= $u['id'] ?>" onclick="return confirm('Supprimer ?')">Supprimer</a></td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html> 