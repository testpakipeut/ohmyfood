<?php
session_start();
$asset_prefix = (strpos($_SERVER['PHP_SELF'], '/pages/') !== false ? '../' : '');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ohmyfood - <?= $pageTitle ?? 'Accueil' ?></title>
    <link rel="stylesheet" href="<?= $asset_prefix ?>assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        function logWidth(selector) {
            var el = document.querySelector(selector);
            if (el) {
                console.log(selector + ' width:', el.offsetWidth + 'px', 'left:', el.getBoundingClientRect().left + 'px');
            } else {
                console.log(selector + ' not found');
            }
        }
        logWidth('html');
        logWidth('body');
        logWidth('.container');
        logWidth('.main-header');
        logWidth('.main-header .container');
        logWidth('.restaurant-grid');
        var card = document.querySelector('.restaurant-card');
        if (card) {
            console.log('.restaurant-card (first) width:', card.offsetWidth + 'px', 'left:', card.getBoundingClientRect().left + 'px');
        } else {
            console.log('.restaurant-card not found');
        }
        const styleSheets = Array.from(document.styleSheets).map(s => s.href);
        console.log('Feuilles de style chargées:', styleSheets);
        console.log('main-header:', document.querySelector('.main-header'));
        console.log('main-nav:', document.querySelector('.main-nav'));
        console.log('main-content:', document.querySelector('.main-content'));
        console.log('restaurant-grid:', document.querySelector('.restaurant-grid'));
        console.log('restaurant-card count:', document.querySelectorAll('.restaurant-card').length);
        console.log('Body classes:', document.body.className);
        window.onerror = function(msg, url, line, col, error) {
            console.error('Erreur JS:', msg, 'à', url+':'+line+':'+col, error);
        };
    });
    </script>
</head>
<body>
    <header class="bg-white shadow-md fixed w-full top-0 z-50">
        <nav class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
            <!-- Logo -->
            <div class="text-3xl font-extrabold tracking-tight">
                <img src="https://i.ibb.co/0ykMt581/logo-ohmyfood.png" class="h-10 w-auto" alt="OhMyFood" />
            </div>
            <!-- Desktop Nav -->
            <div class="hidden md:flex items-center space-x-8">
                <a href="/index.php" class="text-primary font-medium hover:text-secondary transition-colors">Accueil</a>
                <a href="pages/about.php" class="text-primary font-medium hover:text-secondary transition-colors">À propos</a>
                <a href="pages/restaurants.php" class="text-primary font-medium hover:text-secondary transition-colors">Restaurants</a>
                <a href="pages/reservation.php" class="bg-secondary text-primary font-bold px-5 py-2 rounded-lg shadow hover:bg-primary hover:text-secondary transition-colors duration-200">
                    Réserver
                </a>
            </div>
            <!-- Mobile menu button -->
            <button class="md:hidden text-primary">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
        </nav>
    </header>
    <main class="pt-32 pb-12">
    <div class="restaurant-grid">
    ...
    <div class="restaurant-card"> 