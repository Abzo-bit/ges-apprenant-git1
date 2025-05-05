<?php

// Afficher les erreurs pendant le développement
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Point d'entrée de l'application
require_once __DIR__ . '/../app/route/route.web.php';
require_once __DIR__ . '/../app/services/session.service.php';

global $session_services;
$session_services['start_session']();

// Liste des pages qui ne nécessitent pas d'authentification
$public_pages = ['login', 'login-process', 'forgot-password', 'forgot-password-process', 'reset-password', 'reset-password-process'];

// Récupération de la page demandée
$page = isset($_GET['page']) ? $_GET['page'] : 'login';

error_log("Page demandée : $page, Utilisateur connecté : " . ($session_services['is_logged_in']() ? 'Oui' : 'Non'));

// Si l'utilisateur est connecté et qu'il essaie d'accéder à la page de connexion, rediriger vers le dashboard
if ($session_services['is_logged_in']() && in_array($page, $public_pages)) {
    header('Location: ?page=dashboard');
    exit;
}

// Routage vers le contrôleur approprié
$pages = [
    'promotions' => 'list_promotions',
    'add_promotion' => 'add_promotion',
    'add_promotion_process' => 'add_promotion_process',
    'toggle_promotion' => 'toggle_promotion_status',
    'search_referentiels' => 'search_referentiels'
];

// Charger le contrôleur des promotions si nécessaire
if (in_array($page, array_keys($pages))) {
    require_once __DIR__ . '/../app/controllers/promotion.controller.php';
}

// Utilisation de match au lieu de switch
$result = match($page) {
    'promotions' => \App\Controllers\list_promotions(),
    'add_promotion' => \App\Controllers\add_promotion(),
    'add_promotion_process' => \App\Controllers\add_promotion_process(),
    'toggle_promotion' => \App\Controllers\toggle_promotion_status(),
    'search_referentiels' => \App\Controllers\search_referentiels(),
    default => App\Route\route($page)
};