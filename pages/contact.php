<?php
require_once '../config/database.php';
$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Création de la table message si elle n'existe pas
    $conn->query("CREATE TABLE IF NOT EXISTS message (
        id INT PRIMARY KEY AUTO_INCREMENT,
        nom VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL,
        sujet VARCHAR(100) NOT NULL,
        contenu TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    $nom = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $sujet = trim($_POST['subject'] ?? '');
    $contenu = trim($_POST['message'] ?? '');

    if ($nom && $email && $sujet && $contenu) {
        $stmt = $conn->prepare("INSERT INTO message (nom, email, sujet, contenu) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $nom, $email, $sujet, $contenu);
        if ($stmt->execute()) {
            $success = "Votre message a bien été envoyé.";
        } else {
            $error = "Erreur lors de l'envoi du message.";
        }
    } else {
        $error = "Merci de remplir tous les champs.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact - OhMyFood</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
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
        </div>
        <button class="md:hidden text-primary">
          <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
          </svg>
        </button>
      </nav>
    </header>
    <main class="pt-32 pb-12">
      <section class="max-w-5xl mx-auto px-6">
        <h1 class="text-4xl font-bold text-center mb-10">Contactez-nous</h1>
        <div class="grid md:grid-cols-2 gap-8">
          <div class="bg-white rounded-lg shadow-lg p-8">
            <h2 class="text-2xl font-semibold text-primary mb-4">Nos Coordonnées</h2>
            <div class="mb-4 flex items-start gap-3"><span class="text-primary"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 12.414a2 2 0 0 0-2.828 0l-4.243 4.243a8 8 0 1 1 11.314 0z"/></svg></span><div><h3 class="font-bold">Adresse</h3><p>123 Rue de la Gastronomie<br>75001 Paris, France</p></div></div>
            <div class="mb-4 flex items-start gap-3"><span class="text-primary"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 0 0 2.22 0L21 8"/></svg></span><div><h3 class="font-bold">Email</h3><p>contact@ohmyfood.fr</p></div></div>
            <div class="mb-4 flex items-start gap-3"><span class="text-primary"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2zm0 0V3a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v2"/></svg></span><div><h3 class="font-bold">Téléphone</h3><p>01 23 45 67 89</p></div></div>
            <div class="mb-4 flex items-start gap-3"><span class="text-primary"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3"/></svg></span><div><h3 class="font-bold">Horaires</h3><p>Lundi - Vendredi : 9h - 18h<br>Samedi : 10h - 16h</p></div></div>
            <div class="mt-6">
              <h3 class="font-bold mb-2">Suivez-nous</h3>
              <div class="flex gap-4">
                <a href="#" class="text-primary hover:text-secondary"><svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M18.77,7.46H14.5v-1.9c0-.9.6-1.1,1-1.1h3V.5L14.5.5C10.45.5,9.5,3.86,9.5,6.21V7.46h-3v4h3v9.5h5v-9.5h3.85l.42-4Z"/></svg></a>
                <a href="#" class="text-primary hover:text-secondary"><svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12,2.16c3.2,0,3.58,0,4.85.07,3.25.15,4.77,1.69,4.92,4.92.06,1.27.07,1.65.07,4.85s0,3.58-.07,4.85c-.15,3.23-1.69,4.77-4.92,4.92-1.27.06-1.65.07-4.85.07s-3.58,0-4.85-.07c-3.26-.15-4.77-1.7-4.92-4.92-.06-1.27-.07-1.65-.07-4.85s0-3.58.07-4.85C2.38,3.92,3.92,2.38,7.15,2.23,8.42,2.18,8.8,2.16,12,2.16ZM12,0C8.74,0,8.33.01,7.05.07c-4.27.2-6.78,2.71-6.98,6.98C0,8.33,0,8.74,0,12s0,3.67.07,4.95c.2,4.27,2.71,6.78,6.98,6.98,1.28.06,1.69.07,4.95.07s3.67,0,4.95-.07c4.27-.2,6.78-2.71,6.98-6.98.06-1.28.07-1.69.07-4.95s0-3.67-.07-4.95c-.2-4.27-2.71-6.78-6.98-6.98C15.67.01,15.26,0,12,0Zm0,5.84A6.16,6.16,0,1,0,18.16,12,6.16,6.16,0,0,0,12,5.84ZM12,16a4,4,0,1,1,4-4A4,4,0,0,1,12,16ZM18.41,4.15a1.44,1.44,0,1,0,1.44,1.44A1.44,1.44,0,0,0,18.41,4.15Z"/></svg></a>
                <a href="#" class="text-primary hover:text-secondary"><svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M23.95,4.57a10,10,0,0,1-2.82.77,4.96,4.96,0,0,0,2.16-2.72,9.9,9.9,0,0,1-3.12,1.19A4.92,4.92,0,0,0,11.8,9.5,13.94,13.94,0,0,1,1.64,2.9,4.92,4.92,0,0,0,3.2,9.44,4.9,4.9,0,0,1,1,8.84V8.91a4.92,4.92,0,0,0,3.95,4.82,4.94,4.94,0,0,1-2.22.08A4.93,4.93,0,0,0,7.29,17.5,9.9,9.9,0,0,1,0,19.54,13.94,13.94,0,0,0,7.55,21.5c9.06,0,14-7.5,14-14C21.55,7.06,21.55,7,21.54,6.93A9.94,9.94,0,0,0,23.95,4.57Z"/></svg></a>
              </div>
            </div>
          </div>
          <div class="bg-white rounded-lg shadow-lg p-8">
            <h2 class="text-2xl font-semibold text-primary mb-4">Envoyez-nous un message</h2>
            <?php if (!empty($success)): ?>
              <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4"><?= htmlspecialchars($success) ?></div>
            <?php elseif (!empty($error)): ?>
              <div class="bg-red-100 text-red-800 px-4 py-2 rounded mb-4"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <form method="post" class="space-y-4">
              <div>
                <label for="name" class="block font-medium mb-1">Nom complet</label>
                <input type="text" id="name" name="name" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary">
              </div>
              <div>
                <label for="email" class="block font-medium mb-1">Email</label>
                <input type="email" id="email" name="email" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary">
              </div>
              <div>
                <label for="subject" class="block font-medium mb-1">Sujet</label>
                <select id="subject" name="subject" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary">
                  <option value="">Choisir un sujet</option>
                  <option value="general">Question générale</option>
                  <option value="support">Support technique</option>
                  <option value="partnership">Partenariat</option>
                  <option value="other">Autre</option>
                </select>
              </div>
              <div>
                <label for="message" class="block font-medium mb-1">Message</label>
                <textarea id="message" name="message" rows="5" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary"></textarea>
              </div>
              <button type="submit" class="bg-secondary text-primary font-bold px-6 py-2 rounded-lg shadow hover:bg-primary hover:text-secondary transition-colors">Envoyer le message</button>
            </form>
          </div>
        </div>
      </section>
    </main>
   
</body>
</html> 