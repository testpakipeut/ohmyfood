<?php
session_start();
// $pageTitle = "Restaurant";
// require_once '../includes/header.php';
require_once '../config/database.php';

// Vérifier l'ID du restaurant
$restaurant_id = intval($_GET['id'] ?? 0);
if (!$restaurant_id) {
    header('Location: restaurants.php');
    exit;
}

// Récupérer les informations du restaurant
$stmt = $conn->prepare("SELECT * FROM restaurants WHERE id = ?");
$stmt->bind_param("i", $restaurant_id);
$stmt->execute();
$restaurant = $stmt->get_result()->fetch_assoc();

if (!$restaurant) {
    header('Location: restaurants.php');
    exit;
}

$pageTitle = $restaurant['nom'];

// Traitement du formulaire de réservation
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reserve'])) {
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
        exit;
    }

    $date = $_POST['date'] ?? '';
    $time = $_POST['time'] ?? '';
    $guests = intval($_POST['guests'] ?? 0);
    $notes = $_POST['notes'] ?? '';
    $telephone = $_POST['telephone'] ?? '';

    if (empty($date) || empty($time) || $guests < 1 || empty($telephone)) {
        $error = "Veuillez remplir tous les champs obligatoires";
    } else {
        $datetime = date('Y-m-d H:i:s', strtotime("$date $time"));
        
        // Vérifier si la date est dans le futur
        if (strtotime($datetime) <= time()) {
            $error = "La date de réservation doit être dans le futur";
        } else {
            // Récupérer les informations de l'utilisateur
            $stmt = $conn->prepare("SELECT nom, email FROM utilisateurs WHERE id = ?");
            $stmt->bind_param("i", $_SESSION['user_id']);
            $stmt->execute();
            $user = $stmt->get_result()->fetch_assoc();

            $stmt = $conn->prepare("INSERT INTO reservations (restaurant_id, nom, email, telephone, date_reservation, nombre_personnes, message, statut) VALUES (?, ?, ?, ?, ?, ?, ?, 'en_attente')");
            $stmt->bind_param("issssis", $restaurant_id, $user['nom'], $user['email'], $telephone, $datetime, $guests, $notes);
            
            if ($stmt->execute()) {
                $success = "Réservation effectuée avec succès !";
            } else {
                $error = "Erreur lors de la réservation";
            }
        }
    }
}

// Traitement du formulaire d'avis
$avis_error = '';
if (isset($_POST['add_review']) && isset($_SESSION['user_id'])) {
    $prenom = $_SESSION['user_name'] ?? 'Utilisateur';
    $note = intval($_POST['note'] ?? 0);
    $commentaire = trim($_POST['commentaire'] ?? '');
    if ($note < 1 || $note > 5 || empty($commentaire)) {
        $avis_error = "Merci de donner une note et un commentaire.";
    } else {
        // Optionnel : empêcher plusieurs avis par user/restaurant
        $stmt_check = $conn->prepare("SELECT id FROM avis WHERE restaurant_id = ? AND prenom = ?");
        $stmt_check->bind_param("is", $restaurant_id, $prenom);
        $stmt_check->execute();
        if ($stmt_check->get_result()->num_rows > 0) {
            $avis_error = "Vous avez déjà laissé un avis pour ce restaurant.";
        } else {
            $stmt_add = $conn->prepare("INSERT INTO avis (restaurant_id, prenom, note, commentaire) VALUES (?, ?, ?, ?)");
            $stmt_add->bind_param("isis", $restaurant_id, $prenom, $note, $commentaire);
            $stmt_add->execute();
            header("Location: restaurant.php?id=$restaurant_id");
            exit;
        }
    }
}

// Récupérer les avis du restaurant
$avis = [];
$stmt_avis = $conn->prepare("SELECT prenom, note, commentaire, created_at FROM avis WHERE restaurant_id = ? ORDER BY created_at DESC");
$stmt_avis->bind_param("i", $restaurant_id);
$stmt_avis->execute();
$result_avis = $stmt_avis->get_result();
while ($row = $result_avis->fetch_assoc()) {
    $avis[] = $row;
}

// Récupérer le menu du restaurant
$menu = [
    'entrée' => [],
    'plat' => [],
    'dessert' => []
];
$stmt_menu = $conn->prepare("SELECT type, nom, description, prix FROM menu WHERE restaurant_id = ? ORDER BY type, nom");
$stmt_menu->bind_param("i", $restaurant_id);
$stmt_menu->execute();
$result_menu = $stmt_menu->get_result();
while ($row = $result_menu->fetch_assoc()) {
    $menu[$row['type']][] = $row;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> - OhMyFood</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: '#1A1A68',    // Bleu Prusse
            secondary: '#F0C15C',  // Or pâle
            'light-bg': '#F5F5F5', // Gris clair
            'dark-text': '#333333',
            'pastel-blue': '#99B8D4'
          }
        }
      }
    }
    </script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Log couleurs des boutons de navigation
        ['a[href$="index.php"]', 'a[href$="about.php"]', 'a[href$="restaurants.php"]', 'a[href$="reservation.php"]'].forEach(function(sel) {
            var el = document.querySelector(sel);
            if (el) {
                var style = window.getComputedStyle(el);
                console.log('[NAV]', sel, 'color:', style.color, 'background:', style.backgroundColor);
            }
        });
        // Log couleurs des titres de menu
        document.querySelectorAll('h3.text-secondary').forEach(function(el, i) {
            var style = window.getComputedStyle(el);
            console.log('[MENU] Titre catégorie', i, 'color:', style.color, 'background:', style.backgroundColor);
        });
    });
    </script>
</head>
<body class="bg-light-bg text-dark-text">
    <!-- Header/Menu intégré ici -->
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
            <button class="md:hidden text-primary">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
        </nav>
    </header>
    <main class="pt-32 pb-12 bg-light-bg min-h-screen">
        <section class="max-w-4xl mx-auto px-4">
            <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-8">
                <div class="md:flex">
                    <?php if ($restaurant['image_url']): ?>
                        <img src="<?= htmlspecialchars($restaurant['image_url']) ?>" alt="<?= htmlspecialchars($restaurant['nom']) ?>" class="w-full md:w-1/2 h-64 object-cover">
                    <?php endif; ?>
                    <div class="p-8 flex-1 flex flex-col justify-center">
                        <h1 class="text-3xl font-bold text-primary mb-2"><?= htmlspecialchars($restaurant['nom']) ?></h1>
                        <p class="text-gray-600 mb-4 text-lg"><?= htmlspecialchars($restaurant['description']) ?></p>
                        <p class="flex items-center text-gray-500 mb-4">
                            <svg class="w-5 h-5 mr-2 text-primary" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2C6.13 2 3 5.13 3 9c0 5.25 7 11 7 11s7-5.75 7-11c0-3.87-3.13-7-7-7zm0 9.5A2.5 2.5 0 1 1 10 6a2.5 2.5 0 0 1 0 5.5z"/></svg>
                            <?= htmlspecialchars($restaurant['adresse']) ?>
                        </p>
                    </div>
                </div>
            </div>
            <?php if ($success): ?>
                <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">
                    <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="bg-red-100 text-red-800 px-4 py-2 rounded mb-4">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
            <div class="bg-white rounded-xl shadow p-8 mb-8">
                <h2 class="text-2xl font-bold text-primary mb-6">Réserver une table</h2>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <form method="post" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="date" class="block font-semibold mb-1">Date</label>
                            <input type="date" id="date" name="date" required min="<?= date('Y-m-d') ?>" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary">
                        </div>
                        <div>
                            <label for="time" class="block font-semibold mb-1">Heure</label>
                            <input type="time" id="time" name="time" required min="11:00" max="23:00" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary">
                        </div>
                        <div>
                            <label for="guests" class="block font-semibold mb-1">Nombre de personnes</label>
                            <input type="number" id="guests" name="guests" required min="1" max="20" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary">
                        </div>
                        <div>
                            <label for="telephone" class="block font-semibold mb-1">Téléphone</label>
                            <input type="tel" id="telephone" name="telephone" required pattern="[0-9]{10}" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary">
                        </div>
                        <div class="md:col-span-2">
                            <label for="notes" class="block font-semibold mb-1">Notes spéciales (allergies, préférences...)</label>
                            <textarea id="notes" name="notes" rows="3" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary"></textarea>
                        </div>
                        <div class="md:col-span-2 flex justify-end">
                            <button type="submit" name="reserve" class="bg-secondary text-primary font-bold px-6 py-2 rounded-lg shadow hover:bg-primary hover:text-secondary transition-colors">Réserver</button>
                        </div>
                    </form>
                <?php else: ?>
                    <div class="text-center">
                        <p class="mb-2">Vous devez être connecté pour réserver une table.</p>
                        <a href="login.php?redirect=<?= urlencode($_SERVER['REQUEST_URI']) ?>" class="bg-primary text-white px-5 py-2 rounded-lg shadow hover:bg-secondary hover:text-primary transition-colors font-bold">Se connecter</a>
                    </div>
                <?php endif; ?>
            </div>
            <div class="bg-white rounded-xl shadow p-8 mb-8">
                <h2 class="text-2xl font-bold text-primary mb-6">Menu</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <?php foreach(['entrée','plat','dessert'] as $cat): ?>
                        <div class="bg-light-bg rounded-lg shadow-sm p-4 mb-6">
                            <h3 class="text-xl font-semibold text-secondary mb-4 text-center uppercase tracking-wide"><?= strtoupper($cat) ?><?= $cat==='entrée'?'S':'' ?></h3>
                            <?php if (empty($menu[$cat])): ?>
                                <p class="text-gray-400 text-center">Aucun <?= $cat ?> proposé.</p>
                            <?php else: ?>
                                <ul class="divide-y divide-gray-200">
                                    <?php foreach($menu[$cat] as $i => $item): ?>
                                        <li class="flex flex-col py-4 hover:bg-pastel-blue/10 transition rounded">
                                            <div class="flex justify-between w-full items-center">
                                                <span class="font-bold text-primary text-lg"><?= htmlspecialchars($item['nom']) ?></span>
                                                <span class="text-secondary font-bold text-right ml-4 min-w-[70px]"><?= number_format($item['prix'],2,',',' ') ?> €</span>
                                            </div>
                                            <span class="text-gray-600 text-sm mt-1"><?= htmlspecialchars($item['description']) ?></span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow p-8">
                <h2 class="text-2xl font-bold text-primary mb-6">Avis des clients</h2>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <form method="post" class="mb-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="note" class="block font-semibold mb-1">Note</label>
                            <select name="note" id="note" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary">
                                <option value="">Choisir une note</option>
                                <?php for($i=5;$i>=1;$i--): ?>
                                    <option value="<?= $i ?>"><?= $i ?> étoile<?= $i>1?'s':'' ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div>
                            <label for="commentaire" class="block font-semibold mb-1">Commentaire</label>
                            <textarea name="commentaire" id="commentaire" rows="2" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary"></textarea>
                        </div>
                        <div class="md:col-span-2 flex justify-end">
                            <button type="submit" name="add_review" class="bg-secondary text-primary font-bold px-6 py-2 rounded-lg shadow hover:bg-primary hover:text-secondary transition-colors">Envoyer l'avis</button>
                        </div>
                    </form>
                <?php endif; ?>
                <?php if (empty($avis)): ?>
                    <p class="text-gray-500">Aucun avis pour ce restaurant.</p>
                <?php else: ?>
                    <div class="space-y-4">
                        <?php foreach($avis as $a): ?>
                            <div class="bg-light-bg rounded-lg p-4">
                                <div class="flex items-center mb-2">
                                    <strong class="mr-2 text-primary"><?= htmlspecialchars($a['prenom']) ?></strong>
                                    <span class="text-yellow-400">
                                        <?php for($i=0;$i<$a['note'];$i++) echo '★'; ?><?php for($i=$a['note'];$i<5;$i++) echo '☆'; ?>
                                    </span>
                                </div>
                                <p class="mb-1 text-gray-700">"<?= htmlspecialchars($a['commentaire']) ?>"</p>
                                <small class="text-gray-400">Posté le <?= date('d/m/Y', strtotime($a['created_at'])) ?></small>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </main>
    <!-- Footer -->
    <footer class="bg-primary text-white py-12">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-xl font-bold mb-4">OhMyFood</h3>
                    <p class="text-gray-300">L'excellence gastronomique à portée de clic</p>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Liens utiles</h4>
                    <ul class="space-y-2">
                        <li><a href="about.php" class="text-gray-300 hover:text-white">À propos</a></li>
                        <li><a href="comment-ca-marche.php" class="text-gray-300 hover:text-white">Comment ça marche</a></li>
                        <li><a href="restaurants-partenaires.php" class="text-gray-300 hover:text-white">Restaurants partenaires</a></li>
                        <li><a href="contact.php" class="text-gray-300 hover:text-white">Contact</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Légal</h4>
                    <ul class="space-y-2">
                        <li><a href="cgu.php" class="text-gray-300 hover:text-white">CGU</a></li>
                        <li><a href="politique-confidentialite.php" class="text-gray-300 hover:text-white">Politique de confidentialité</a></li>
                        <li><a href="mentions-legales.php" class="text-gray-300 hover:text-white">Mentions légales</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Suivez-nous</h4>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-300 hover:text-white transition-colors">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M18.77,7.46H14.5v-1.9c0-.9.6-1.1,1-1.1h3V.5L14.5.5C10.45.5,9.5,3.86,9.5,6.21V7.46h-3v4h3v9.5h5v-9.5h3.85l.42-4Z"/></svg>
                        </a>
                        <a href="#" class="text-gray-300 hover:text-white transition-colors">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12,2.16c3.2,0,3.58,0,4.85.07,3.25.15,4.77,1.69,4.92,4.92.06,1.27.07,1.65.07,4.85s0,3.58-.07,4.85c-.15,3.23-1.69,4.77-4.92,4.92-1.27.06-1.65.07-4.85.07s-3.58,0-4.85-.07c-3.26-.15-4.77-1.7-4.92-4.92-.06-1.27-.07-1.65-.07-4.85s0-3.58.07-4.85C2.38,3.92,3.92,2.38,7.15,2.23,8.42,2.18,8.8,2.16,12,2.16ZM12,0C8.74,0,8.33.01,7.05.07c-4.27.2-6.78,2.71-6.98,6.98C0,8.33,0,8.74,0,12s0,3.67.07,4.95c.2,4.27,2.71,6.78,6.98,6.98,1.28.06,1.69.07,4.95.07s3.67,0,4.95-.07c4.27-.2,6.78-2.71,6.98-6.98.06-1.28.07-1.69.07-4.95s0-3.67-.07-4.95c-.2-4.27-2.71-6.78-6.98-6.98C15.67.01,15.26,0,12,0Zm0,5.84A6.16,6.16,0,1,0,18.16,12,6.16,6.16,0,0,0,12,5.84ZM12,16a4,4,0,1,1,4-4A4,4,0,0,1,12,16ZM18.41,4.15a1.44,1.44,0,1,0,1.44,1.44A1.44,1.44,0,0,0,18.41,4.15Z"/></svg>
                        </a>
                        <a href="#" class="text-gray-300 hover:text-white transition-colors">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M23.95,4.57a10,10,0,0,1-2.82.77,4.96,4.96,0,0,0,2.16-2.72,9.9,9.9,0,0,1-3.12,1.19A4.92,4.92,0,0,0,11.8,9.5,13.94,13.94,0,0,1,1.64,2.9,4.92,4.92,0,0,0,3.2,9.44,4.9,4.9,0,0,1,1,8.84V8.91a4.92,4.92,0,0,0,3.95,4.82,4.94,4.94,0,0,1-2.22.08A4.93,4.93,0,0,0,7.29,17.5,9.9,9.9,0,0,1,0,19.54,13.94,13.94,0,0,0,7.55,21.5c9.06,0,14-7.5,14-14C21.55,7.06,21.55,7,21.54,6.93A9.94,9.94,0,0,0,23.95,4.57Z"/></svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </footer>
</body>
</html> 