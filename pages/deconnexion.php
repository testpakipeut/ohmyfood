<?php
session_start();
session_destroy();
header('Location: ../index.php');
exit();
?>

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