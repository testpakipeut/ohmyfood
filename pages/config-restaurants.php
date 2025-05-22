<?php
// Page de configuration cachée pour les restaurants
require_once '../config/database.php';

// Création de la table si elle n'existe pas
$pdo->exec("CREATE TABLE IF NOT EXISTS restaurants (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    location VARCHAR(255),
    image LONGBLOB,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// Création de la table avis si elle n'existe pas
$pdo->exec("CREATE TABLE IF NOT EXISTS avis (
    id INT PRIMARY KEY AUTO_INCREMENT,
    restaurant_id INT NOT NULL,
    prenom VARCHAR(50) NOT NULL,
    note INT NOT NULL,
    commentaire TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (restaurant_id) REFERENCES restaurants(id) ON DELETE CASCADE
)");

// Générer 5 avis fictifs pour chaque restaurant si aucun avis n'existe
$prenoms = ['Sophie','Lucas','Emma','Hugo','Chloé','Léa','Louis','Manon','Jules','Camille'];
$commentaires = [
    "Excellent, service parfait et plats délicieux !",
    "Une expérience inoubliable, je recommande vivement.",
    "Cadre agréable et personnel attentionné.",
    "Cuisine raffinée, présentation soignée.",
    "Un vrai régal du début à la fin !"
];

$stmt = $pdo->query("SELECT id FROM restaurants");
$restaurants = $stmt->fetchAll();

foreach ($restaurants as $row) {
    $rid = $row['id'];
    $stmt = $pdo->prepare("SELECT COUNT(*) as nb FROM avis WHERE restaurant_id = ?");
    $stmt->execute([$rid]);
    $avis_count = $stmt->fetch()['nb'];
    
    if ($avis_count < 5) {
        for ($i=0; $i<5; $i++) {
            $prenom = $prenoms[array_rand($prenoms)];
            $note = rand(3,5);
            $commentaire = $commentaires[$i];
            $stmt = $pdo->prepare("INSERT INTO avis (restaurant_id, prenom, note, commentaire) VALUES (?, ?, ?, ?)");
            $stmt->execute([$rid, $prenom, $note, $commentaire]);
        }
    }
}

// Ajout d'un restaurant
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_restaurant'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $location = $_POST['location'];
    $image = null;
    if (isset($_FILES['image']) && $_FILES['image']['tmp_name']) {
        $image = file_get_contents($_FILES['image']['tmp_name']);
    }
    $stmt = $pdo->prepare("INSERT INTO restaurants (name, description, location, image) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssb", $name, $description, $location, $image);
    $stmt->send_long_data(3, $image);
    $stmt->execute();
}

// Suppression d'un restaurant
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $pdo->exec("DELETE FROM restaurants WHERE id = $id");
}

// Liste des restaurants
$stmt = $pdo->query("SELECT id, name, description, location, image FROM restaurants ORDER BY id DESC");
$restaurants = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Config Restaurants</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>body{padding-top:40px;max-width:700px;margin:auto;} img{max-width:100px;max-height:60px;object-fit:cover;}</style>
</head>
<body>
    <h1>Configuration des restaurants</h1>
    <form method="post" enctype="multipart/form-data" class="form card">
        <div class="form-group">
            <label>Nom</label>
            <input type="text" name="name" required>
        </div>
        <div class="form-group">
            <label>Description</label>
            <textarea name="description" required></textarea>
        </div>
        <div class="form-group">
            <label>Adresse</label>
            <input type="text" name="location" required>
        </div>
        <div class="form-group">
            <label>Photo (JPG/PNG)</label>
            <input type="file" name="image" accept="image/*">
        </div>
        <button type="submit" name="add_restaurant" class="btn btn-primary">Ajouter</button>
    </form>
    <h2>Liste des restaurants</h2>
    <table border="1" cellpadding="6" style="width:100%;background:#fff;">
        <tr><th>ID</th><th>Nom</th><th>Adresse</th><th>Image</th><th>Action</th></tr>
        <?php foreach($restaurants as $r): ?>
        <tr>
            <td><?= $r['id'] ?></td>
            <td><?= htmlspecialchars($r['name']) ?></td>
            <td><?= htmlspecialchars($r['location']) ?></td>
            <td><?php if ($r['image']) echo '<img src="data:image/jpeg;base64,'.base64_encode($r['image']).'"/>'; ?></td>
            <td><a href="?delete=<?= $r['id'] ?>" onclick="return confirm('Supprimer ?')">Supprimer</a></td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html> 