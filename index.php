<?php
require_once __DIR__ . '/config/database.php';
session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OhMyFood - Réservation de Restaurants Gastronomiques</title>
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
            img.src = 'assets/images/default-restaurant.jpg';
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

        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'primary': '#1A1A68',    // Bleu Prusse
                        'secondary': '#F0C15C',   // Or pâle
                        'light-bg': '#F5F5F5',    // Gris clair
                        'dark-text': '#333333',   // Gris foncé
                        'pastel-blue': '#99B8D4', // Bleu pastel
                    }
                }
            }
        }
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const mobileMenu = document.getElementById('mobile-menu');
            
            mobileMenuButton.addEventListener('click', function() {
                mobileMenu.classList.toggle('hidden');
            });

            // Fermer le menu mobile quand on clique en dehors
            document.addEventListener('click', function(event) {
                if (!mobileMenuButton.contains(event.target) && !mobileMenu.contains(event.target)) {
                    mobileMenu.classList.add('hidden');
                }
            });
        });
    </script>
</head>
<body class="bg-light-bg text-dark-text">
    <!-- Header -->
    <header class="bg-white shadow-md fixed w-full top-0 z-50">
        <nav class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
            <!-- Logo -->
            <div class="h-full flex items-center justify-center">
                <img src="https://i.postimg.cc/4Ny6gmBd/logoohmyfood.png" class="max-h-full object-contain" alt="OhMyFood" />
            </div>
            <!-- Desktop Nav -->
            <div class="hidden md:flex items-center space-x-8">
                <a href="index.php" class="text-primary font-medium hover:text-secondary transition-colors">Accueil</a>
                <a href="pages/about.php" class="text-primary font-medium hover:text-secondary transition-colors">À propos</a>
                <a href="pages/restaurants.php" class="text-primary font-medium hover:text-secondary transition-colors">Restaurants</a>
                <a href="pages/restaurants.php" class="bg-secondary text-primary font-bold px-5 py-2 rounded-lg shadow hover:bg-primary hover:text-secondary transition-colors duration-200">Réserver</a>
                <?php if (empty($_SESSION['user_id'])): ?>
                    <a href="pages/login.php" class="ml-4 bg-primary text-white font-bold px-5 py-2 rounded-lg shadow hover:bg-secondary hover:text-primary transition-colors duration-200">Connexion</a>
                    <a href="pages/register.php" class="ml-2 bg-secondary text-primary font-bold px-5 py-2 rounded-lg shadow hover:bg-primary hover:text-secondary transition-colors duration-200">Inscription</a>
                <?php else: ?>
                    <a href="pages/mes-reservations.php" class="ml-4 bg-secondary text-primary font-bold px-5 py-2 rounded-lg shadow hover:bg-primary hover:text-secondary transition-colors duration-200">Mes réservations</a>
                    <span class="ml-4 text-primary flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A9 9 0 1112 21a9 9 0 01-6.879-3.196z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        Bonjour, <?= htmlspecialchars($_SESSION['user_name']) ?>
                    </span>
                    <a href="pages/logout.php" class="ml-2 bg-primary text-white font-bold px-5 py-2 rounded-lg shadow hover:bg-secondary hover:text-primary transition-colors duration-200">Déconnexion</a>
                <?php endif; ?>
            </div>
            <!-- Mobile menu button -->
            <button id="mobile-menu-button" class="block md:hidden text-primary">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
        </nav>
        <!-- Mobile menu -->
        <div id="mobile-menu" class="hidden md:hidden bg-white border-t">
            <div class="px-4 py-3 space-y-3">
                <a href="index.php" class="block text-primary font-medium hover:text-secondary transition-colors">Accueil</a>
                <a href="pages/about.php" class="block text-primary font-medium hover:text-secondary transition-colors">À propos</a>
                <a href="pages/restaurants.php" class="block text-primary font-medium hover:text-secondary transition-colors">Restaurants</a>
                <a href="pages/restaurants.php" class="block bg-secondary text-primary font-bold px-5 py-2 rounded-lg shadow hover:bg-primary hover:text-secondary transition-colors duration-200">Réserver</a>
                <?php if (empty($_SESSION['user_id'])): ?>
                    <a href="pages/login.php" class="block bg-primary text-white font-bold px-5 py-2 rounded-lg shadow hover:bg-secondary hover:text-primary transition-colors duration-200">Connexion</a>
                    <a href="pages/register.php" class="block bg-secondary text-primary font-bold px-5 py-2 rounded-lg shadow hover:bg-primary hover:text-secondary transition-colors duration-200">Inscription</a>
                <?php else: ?>
                    <a href="pages/mes-reservations.php" class="block bg-secondary text-primary font-bold px-5 py-2 rounded-lg shadow hover:bg-primary hover:text-secondary transition-colors duration-200">Mes réservations</a>
                    <div class="flex items-center gap-2 text-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A9 9 0 1112 21a9 9 0 01-6.879-3.196z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        <span>Bonjour, <?= htmlspecialchars($_SESSION['user_name']) ?></span>
                    </div>
                    <a href="pages/logout.php" class="block bg-primary text-white font-bold px-5 py-2 rounded-lg shadow hover:bg-secondary hover:text-primary transition-colors duration-200">Déconnexion</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="bg-primary text-white pt-32 pb-20">
        <div class="max-w-7xl mx-auto px-6 text-center">
            <h1 class="text-4xl md:text-6xl font-bold mb-8">
                Découvrez les meilleures tables autour de vous
            </h1>
            <div class="max-w-2xl mx-auto">
                <form action="pages/restaurants.php" method="GET" class="bg-white rounded-lg p-2 flex items-center">
                    <input type="text" name="search" placeholder="Ville, type de cuisine, restaurant..." 
                           class="w-full px-4 py-2 text-dark-text focus:outline-none">
                    <button class="bg-secondary text-primary px-6 py-2 rounded-lg hover:bg-opacity-90 transition-colors">
                        Rechercher
                    </button>
                </form>
            </div>
        </div>
    </section>

    <!-- Featured Restaurants -->
    <section class="py-16">
        <div class="max-w-7xl mx-auto px-6">
            <h2 class="text-3xl font-bold mb-12 text-center">Restaurants populaires</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php
                $stmt = $pdo->query("SELECT * FROM restaurants ORDER BY id DESC LIMIT 6");
                $featured_restaurants = $stmt->fetchAll();

                // Debug des données des restaurants
                foreach ($featured_restaurants as $index => $restaurant) {
                    error_log("Restaurant {$index}: " . $restaurant['nom']);
                    error_log("Image présente: " . (!empty($restaurant['image_url']) ? 'Oui' : 'Non'));
                }

                foreach($featured_restaurants as $restaurant): ?>
                <div class="bg-white rounded-lg shadow-lg overflow-hidden transform hover:scale-105 transition-transform duration-300">
                    <div class="relative">
                        <?php if (!empty($restaurant['image_url'])): ?>
                            <img src="<?= htmlspecialchars($restaurant['image_url']) ?>" 
                                 alt="<?= htmlspecialchars($restaurant['nom']) ?>" 
                                 class="w-full h-48 object-cover"
                                 onerror="handleImageError(this)"
                                 onload="handleImageLoad(this)">
                        <?php else: ?>
                            <img src="assets/images/default-restaurant.jpg" 
                                 alt="<?= htmlspecialchars($restaurant['nom']) ?>" 
                                 class="w-full h-48 object-cover">
                        <?php endif; ?>
                        <div class="absolute top-4 right-4 bg-secondary text-primary px-3 py-1 rounded-full text-sm font-semibold">
                            Populaire
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold mb-2"><?= htmlspecialchars($restaurant['nom']) ?></h3>
                        <p class="text-gray-600 mb-4"><?= htmlspecialchars($restaurant['description']) ?></p>
                        <div class="flex justify-between items-center">
                            <div class="flex items-center">
                                <span class="text-secondary">★★★★★</span>
                            </div>
                            <a href="pages/restaurant.php?id=<?= $restaurant['id'] ?>" class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-opacity-90 transition-colors">
                                Voir le restaurant
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- How it Works -->
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-6">
            <h2 class="text-3xl font-bold mb-12 text-center">Comment ça marche</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="bg-primary text-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6">
                        <span class="text-2xl font-bold">1</span>
                    </div>
                    <h3 class="text-xl font-bold mb-4">Recherchez</h3>
                    <p class="text-gray-600">Trouvez le restaurant parfait selon vos critères</p>
                </div>
                <div class="text-center">
                    <div class="bg-primary text-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6">
                        <span class="text-2xl font-bold">2</span>
                    </div>
                    <h3 class="text-xl font-bold mb-4">Réservez</h3>
                    <p class="text-gray-600">Choisissez votre date et confirmez en quelques clics</p>
                </div>
                <div class="text-center">
                    <div class="bg-primary text-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6">
                        <span class="text-2xl font-bold">3</span>
                    </div>
                    <h3 class="text-xl font-bold mb-4">Savourez</h3>
                    <p class="text-gray-600">Profitez d'une expérience gastronomique unique</p>
                </div>
            </div>
        </div>
    </section>

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
                        <li><a href="pages/about.php" class="text-gray-300 hover:text-white">À propos</a></li>
                        <li><a href="pages/comment-ca-marche.php" class="text-gray-300 hover:text-white">Comment ça marche</a></li>
                        <li><a href="pages/restaurants-partenaires.php" class="text-gray-300 hover:text-white">Restaurants partenaires</a></li>
                        <li><a href="pages/contact.php" class="text-gray-300 hover:text-white">Contact</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Légal</h4>
                    <ul class="space-y-2">
                        <li><a href="pages/cgu.php" class="text-gray-300 hover:text-white">CGU</a></li>
                        <li><a href="pages/politique-confidentialite.php" class="text-gray-300 hover:text-white">Politique de confidentialité</a></li>
                        <li><a href="pages/mentions-legales.php" class="text-gray-300 hover:text-white">Mentions légales</a></li>
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