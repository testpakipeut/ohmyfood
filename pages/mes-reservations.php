<?php
session_start();
require_once '../config/database.php';
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

// Annulation d'une réservation
if (isset($_GET['annuler']) && is_numeric($_GET['annuler'])) {
    $reservation_id = intval($_GET['annuler']);
    $stmt = $conn->prepare("UPDATE reservations SET statut = 'annulee' WHERE id = ? AND nom = ?");
    $stmt->bind_param("is", $reservation_id, $user_name);
    $stmt->execute();
}

// Récupérer les réservations de l'utilisateur
$stmt = $conn->prepare("SELECT r.id, r.restaurant_id, r.date_reservation, r.nombre_personnes, r.statut, r.created_at, res.nom AS restaurant_nom FROM reservations r JOIN restaurants res ON r.restaurant_id = res.id WHERE r.nom = ? ORDER BY r.date_reservation DESC");
$stmt->bind_param("s", $user_name);
$stmt->execute();
$reservations = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang='fr'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Mes réservations - OhMyFood</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = {theme: {extend: {colors: {primary: '#1A1A68',secondary: '#F0C15C','light-bg': '#F5F5F5','dark-text': '#333333'}}}};</script>
</head>
<body class="bg-light-bg text-dark-text">
    <header class="bg-white shadow-md fixed w-full top-0 z-50">
        <nav class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
            <div class="h-full flex items-center justify-center">
                <img src="https://i.postimg.cc/4Ny6gmBd/logoohmyfood.png" class="max-h-full object-contain" alt="OhMyFood" />
            </div>
            <div class="hidden md:flex items-center space-x-8">
                <a href="../index.php" class="text-primary font-medium hover:text-secondary transition-colors">Accueil</a>
                <a href="restaurants.php" class="text-primary font-medium hover:text-secondary transition-colors">Restaurants</a>
                <a href="mes-reservations.php" class="bg-secondary text-primary font-bold px-5 py-2 rounded-lg shadow hover:bg-primary hover:text-secondary transition-colors duration-200">Mes réservations</a>
                <span class="ml-4 text-primary flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A9 9 0 1112 21a9 9 0 01-6.879-3.196z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                    Bonjour, <?= htmlspecialchars($user_name) ?>
                </span>
                <a href="logout.php" class="ml-2 bg-primary text-white font-bold px-5 py-2 rounded-lg shadow hover:bg-secondary hover:text-primary transition-colors duration-200">Déconnexion</a>
            </div>
        </nav>
    </header>
    <main class="pt-32 pb-12 max-w-3xl mx-auto px-4">
        <h1 class="text-3xl font-bold text-primary mb-8 text-center">Mes réservations</h1>
        <?php if (empty($reservations)): ?>
            <div class="text-center text-gray-500">Aucune réservation trouvée.</div>
        <?php else: ?>
            <div class="space-y-6">
                <?php foreach($reservations as $res): ?>
                    <div class="bg-white rounded-xl shadow p-6 flex flex-col md:flex-row md:items-center md:justify-between">
                        <div>
                            <div class="font-bold text-lg text-primary mb-1"><?= htmlspecialchars($res['restaurant_nom']) ?></div>
                            <div class="text-gray-600 mb-1">Date : <?= date('d/m/Y H:i', strtotime($res['date_reservation'])) ?></div>
                            <div class="text-gray-600 mb-1">Personnes : <?= $res['nombre_personnes'] ?></div>
                            <div class="text-gray-500 text-sm">Statut : <span class="font-semibold <?= $res['statut']==='annulee'?'text-red-600':($res['statut']==='confirmee'?'text-green-600':'text-yellow-600') ?>"><?= ucfirst($res['statut']) ?></span></div>
                        </div>
                        <?php if ($res['statut'] !== 'annulee'): ?>
                        <a href="?annuler=<?= $res['id'] ?>" class="mt-4 md:mt-0 bg-red-100 text-red-700 font-bold px-4 py-2 rounded-lg shadow hover:bg-red-200 transition-colors" onclick="return confirm('Annuler cette réservation ?');">Annuler</a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
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
                        <li><a href="/OHMYFOOD/pages/about.php" class="text-gray-300 hover:text-white">À propos</a></li>
                        <li><a href="/OHMYFOOD/pages/comment-ca-marche.php" class="text-gray-300 hover:text-white">Comment ça marche</a></li>
                        <li><a href="/OHMYFOOD/pages/restaurants-partenaires.php" class="text-gray-300 hover:text-white">Restaurants partenaires</a></li>
                        <li><a href="/OHMYFOOD/pages/contact.php" class="text-gray-300 hover:text-white">Contact</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Légal</h4>
                    <ul class="space-y-2">
                        <li><a href="/OHMYFOOD/pages/cgu.php" class="text-gray-300 hover:text-white">CGU</a></li>
                        <li><a href="/OHMYFOOD/pages/politique-confidentialite.php" class="text-gray-300 hover:text-white">Politique de confidentialité</a></li>
                        <li><a href="/OHMYFOOD/pages/mentions-legales.php" class="text-gray-300 hover:text-white">Mentions légales</a></li>
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