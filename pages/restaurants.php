<?php
session_start();
// $pageTitle = "Restaurants";
// require_once '../includes/header.php';
require_once '../config/database.php';

// Paramètres de recherche et filtrage
$search = $_GET['search'] ?? '';
$page = max(1, intval($_GET['page'] ?? 1));
$per_page = 9;
$offset = ($page - 1) * $per_page;

// Construction de la requête
$where = [];
$params = [];
$types = '';

if (!empty($search)) {
    $where[] = "(nom LIKE ? OR description LIKE ? OR adresse LIKE ?)";
    $search_param = "%$search%";
    $params = array_merge($params, [$search_param, $search_param, $search_param]);
    $types .= 'sss';
}

$where_clause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

// Requête pour le nombre total de restaurants
$count_sql = "SELECT COUNT(*) as total FROM restaurants $where_clause";
if (!empty($params)) {
    $stmt = $pdo->prepare($count_sql);
    $stmt->execute($params);
    $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
} else {
    $stmt = $pdo->prepare($count_sql);
    $stmt->execute();
    $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
}

$total_pages = ceil($total / $per_page);

// Requête pour les restaurants
$sql = "SELECT * FROM restaurants $where_clause ORDER BY id DESC LIMIT ? OFFSET ?";
$stmt = $pdo->prepare($sql);

if (!empty($params)) {
    $params[] = $per_page;
    $params[] = $offset;
    $stmt->execute($params);
} else {
    $stmt->execute();
}
$restaurants = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Debug des données des restaurants
foreach ($restaurants as $index => $restaurant) {
    error_log("Restaurant {$index}: " . $restaurant['nom']);
    error_log("Image présente: " . (!empty($restaurant['image_url']) ? 'Oui' : 'Non'));
    if (!empty($restaurant['image_url'])) {
        error_log("URL de l'image: " . $restaurant['image_url']);
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurants - OhMyFood</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        console.log('Script de débogage initialisé');
        
        // Fonction pour gérer les erreurs de chargement d'image
        function handleImageError(img) {
            console.error('Erreur de chargement de l\'image:', {
                restaurant: img.alt,
                src: img.src.substring(0, 100) + '...', // Affiche le début de la source pour debug
                base64Length: img.src.length
            });
            
            // Remplacer l'image par un placeholder
            img.onerror = null; // Évite la boucle infinie
            img.src = '../assets/images/placeholder.jpg';
        }

        // Fonction pour confirmer le chargement réussi
        function handleImageLoad(img) {
            console.log('Image chargée avec succès:', {
                restaurant: img.alt,
                dimensions: {
                    naturalWidth: img.naturalWidth,
                    naturalHeight: img.naturalHeight
                }
            });
        }

        // Vérification des données d'image au chargement de la page
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM chargé, recherche des images...');
            const images = document.querySelectorAll('img[src^="data:image/jpeg;base64,"]');
            console.log('Nombre total d\'images trouvées:', images.length);
            
            images.forEach((img, index) => {
                console.log(`Image ${index + 1}:`, {
                    restaurant: img.alt,
                    base64Length: img.src.length,
                    isValidBase64: /^data:image\/jpeg;base64,[A-Za-z0-9+/=]+$/.test(img.src)
                });
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            // Log de la largeur de la page et du contenu principal
            var body = document.body;
            var main = document.querySelector('main');
            var section = document.querySelector('section');
            console.log('[LOG] Largeur body:', body.offsetWidth);
            if(main) console.log('[LOG] Largeur main:', main.offsetWidth, 'Position left:', main.getBoundingClientRect().left);
            if(section) console.log('[LOG] Largeur section:', section.offsetWidth, 'Position left:', section.getBoundingClientRect().left);
            if(main && main.offsetLeft > 0) {
                console.warn('[ALERTE] Le contenu main est décalé de', main.offsetLeft, 'pixels à droite !');
            }
            if(section && section.offsetLeft > 0) {
                console.warn('[ALERTE] Le contenu section est décalé de', section.offsetLeft, 'pixels à droite !');
            }
        });
    </script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#1A1A68',
                        secondary: '#F0C15C',
                        'light-bg': '#F5F5F5',
                        'dark-text': '#333333',
                        'pastel-blue': '#99B8D4'
                    }
                }
            }
        }
    </script>
    <script>
        console.log('Script de débogage initialisé');
        // LOGS AVANCÉS POUR DIAGNOSTIC LAYOUT
        document.addEventListener('DOMContentLoaded', function() {
            function logElementInfo(selector) {
                var el = document.querySelector(selector);
                if (el) {
                    var rect = el.getBoundingClientRect();
                    var styles = window.getComputedStyle(el);
                    console.log(`%c[${selector}]`, 'color: #1A1A68; font-weight: bold;', {
                        width: el.offsetWidth,
                        height: el.offsetHeight,
                        left: rect.left,
                        top: rect.top,
                        marginLeft: styles.marginLeft,
                        marginRight: styles.marginRight,
                        paddingLeft: styles.paddingLeft,
                        paddingRight: styles.paddingRight,
                        display: styles.display,
                        classes: el.className
                    });
                } else {
                    console.warn(`[${selector}] non trouvé`);
                }
            }
            [
                'body',
                'main',
                'section',
                '.max-w-7xl',
                '.grid',
                '.restaurant-grid',
                '.restaurant-card'
            ].forEach(logElementInfo);
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
    <main class="pt-32 pb-12">
      <section class="max-w-7xl mx-auto px-6">
        <!-- Recherche -->
        <h1 class="text-4xl font-bold text-center mb-10">Restaurants</h1>
        <form action="" method="get" class="flex flex-col md:flex-row gap-4 mb-8 max-w-2xl mx-auto">
          <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Rechercher un restaurant..." class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary">
          <button type="submit" class="bg-secondary text-primary font-bold px-6 py-2 rounded-lg shadow hover:bg-primary hover:text-secondary transition-colors">Rechercher</button>
        </form>
        <?php if (empty($restaurants)): ?>
          <div class="text-center text-gray-500">Aucun restaurant trouvé.</div>
        <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          <?php foreach($restaurants as $restaurant): ?>
            <div class="bg-white rounded-lg shadow-lg overflow-hidden transform hover:scale-105 transition-transform duration-300">
              <?php if (!empty($restaurant['image_url'])): ?>
                <img src="<?= htmlspecialchars($restaurant['image_url']) ?>" 
                     alt="<?= htmlspecialchars($restaurant['nom']) ?>" 
                     class="w-full h-48 object-cover"
                     onerror="handleImageError(this)"
                     onload="handleImageLoad(this)">
              <?php else: ?>
                <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                  <span class="text-gray-500">Aucune image disponible</span>
                </div>
              <?php endif; ?>
              <div class="p-6">
                <h3 class="text-xl font-bold mb-2"><?= htmlspecialchars($restaurant['nom']) ?></h3>
                <p class="text-gray-600 mb-4"><?= htmlspecialchars($restaurant['description']) ?></p>
                <p class="text-sm text-gray-500 mb-4 flex items-center">
                  <svg class="w-4 h-4 mr-1 text-primary inline" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 2C6.13 2 3 5.13 3 9c0 5.25 7 11 7 11s7-5.75 7-11c0-3.87-3.13-7-7-7zm0 9.5A2.5 2.5 0 1 1 10 6a2.5 2.5 0 0 1 0 5.5z"/>
                  </svg>
                  <?= htmlspecialchars($restaurant['adresse']) ?>
                </p>
                <div class="flex justify-between items-center">
                  <div class="flex items-center">
                    <span class="text-secondary">★★★★★</span>
                  </div>
                  <a href="restaurant.php?id=<?= $restaurant['id'] ?>" class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-opacity-90 transition-colors">
                    Voir le restaurant
                  </a>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
        <?php if ($total_pages > 1): ?>
          <div class="flex justify-center items-center gap-4 mt-8">
            <?php if ($page > 1): ?>
              <a href="?page=<?= $page-1 ?><?= !empty($search) ? '&search='.urlencode($search) : '' ?>" class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-opacity-90 transition-colors">&larr; Précédent</a>
            <?php endif; ?>
            <span class="text-gray-700">Page <?= $page ?> sur <?= $total_pages ?></span>
            <?php if ($page < $total_pages): ?>
              <a href="?page=<?= $page+1 ?><?= !empty($search) ? '&search='.urlencode($search) : '' ?>" class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-opacity-90 transition-colors">Suivant &rarr;</a>
            <?php endif; ?>
          </div>
        <?php endif; ?>
        <?php endif; ?>
      </section>
    </main>
</body>
</html> 