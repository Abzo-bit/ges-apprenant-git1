<?php

namespace App\Controllers;

require_once __DIR__ . '/controller.php';
require_once __DIR__ . '/../models/model.php';
require_once __DIR__ . '/../services/validator.service.php';
require_once __DIR__ . '/../services/session.service.php';
require_once __DIR__ . '/../translate/fr/error.fr.php';
require_once __DIR__ . '/../translate/fr/message.fr.php';
require_once __DIR__ . '/../enums/profile.enum.php';

use App\Models;
use App\Services;
use App\Translate\fr;
use App\Enums;
use Exception; // Ajoutez cette ligne

// Affichage de la liste des référentiels de la promotion en cours
function list_referentiels() {
    global $model, $session_services;
    
    try {
        // Vérifier l'authentification
        $user = $session_services['get_current_user']();
        if (!$user || !in_array($user['profile'], ['Admin', 'Attache'])) {
            redirect('?page=forbidden');
            return;
        }
        
        // Récupérer la promotion courante
        $current_promotion = $model['get_current_promotion']();
        
        // Si aucune promotion n'est active
        if (!$current_promotion) {
            $session_services['set_flash_message']('info', 'Aucune promotion active');
            redirect('?page=promotions');
            return;
        }
        
        // Récupérer uniquement les référentiels de la promotion courante
        $referentiels = $model['get_referentiels_by_promotion']($current_promotion['id']);
        
        render('admin.layout.php', 'referentiel/list.html.php', [
            'user' => $user,
            'referentiels' => $referentiels,
            'current_promotion' => $current_promotion
        ]);
        
    } catch (Exception $e) {
        $session_services['set_flash_message']('danger', 'Une erreur est survenue');
        redirect('?page=dashboard');
    }
}

// Affichage de la liste de tous les référentiels
function list_all_referentiels() {
    global $model;
    
    $user = check_auth();
    
    // Récupérer le terme de recherche
    $search = $_GET['search'] ?? '';
    
    // Récupération de tous les référentiels
    $all_referentiels = $model['get_all_referentiels']();
    
    // Filtrer les référentiels si un terme de recherche est présent
    $referentiels = $all_referentiels;
    if (!empty($search)) {
        $referentiels = array_filter($all_referentiels, function($ref) use ($search) {
            return (
                stripos($ref['name'], $search) !== false || 
                stripos($ref['description'], $search) !== false
            );
        });
    }
    
    // Affichage de la vue avec les référentiels filtrés
    render('admin.layout.php', 'referentiel/list-all.html.php', [
        'user' => $user,
        'referentiels' => array_values($referentiels),
        'active_menu' => 'referentiels',
        'search' => $search
    ]);
}

function show_referentiel($id) {
    global $model;
    
    $referentiel = $model['get_referentiel_by_id']($id);
    
    if (!$referentiel) {
        redirect('?page=referentiels');
        return;
    }
    
    // Formater l'image si nécessaire
    if (!str_starts_with($referentiel['image'], 'assets/')) {
        $referentiel['image'] = ltrim($referentiel['image'], '/');
    }
    
    render('admin.layout.php', 'referentiel/view.html.php', [
        'referentiel' => $referentiel,
        'active_menu' => 'referentiels'
    ]);
}

// Affichage du formulaire d'ajout d'un référentiel
function add_referentiel_form() {
    global $model;
    
    // Vérification des droits d'accès (Admin uniquement)
    $user = check_profile(Enums\ADMIN);
    
    // Affichage de la vue
    render('admin.layout.php', 'referentiel/add.html.php', [
        'user' => $user
    ]);
}

// Traitement de l'ajout d'un référentiel
function add_referentiel_process() {
    global $model, $validator_services, $session_services;
    
    // Vérification des droits d'accès (Admin uniquement)
    $user = check_profile(Enums\ADMIN);
    
    // Récupération des données du formulaire
    $name = $_POST['referentiel_name'] ?? '';
    $description = $_POST['referentiel_details'] ?? '';
    $promotion = $_POST['promotion'] ?? '';
    
    // Validation des données essentielles
    $errors = [];
    
    if ($validator_services['is_empty']($name)) {
        $errors['name'] = 'Le nom du référentiel est obligatoire';
    } elseif ($model['referentiel_name_exists']($name)) {
        $errors['name'] = 'Un référentiel avec ce nom existe déjà';
    }
    
    if ($validator_services['is_empty']($description)) {
        $errors['description'] = 'La description est obligatoire';
    }
    
    // S'il y a des erreurs, affichage du formulaire avec les erreurs
    if (!empty($errors)) {
        render('admin.layout.php', 'referentiel/list.html.php', [
            'user' => $user,
            'errors' => $errors,
            'name' => $name,
            'description' => $description
        ]);
        return;
    }
    
    // Définir des valeurs par défaut pour les champs manquants
    $referentiel_data = [
        'name' => $name,
        'description' => $description,
        'capacite' => 30,  // Valeur par défaut
        'sessions' => 10,  // Valeur par défaut
        'image' => "assets/images/default-referentiel.jpg"  // Image par défaut
    ];
    
    // Création du référentiel
    $result = $model['create_referentiel']($referentiel_data);
    
    if (!$result) {
        $session_services['set_flash_message']('danger', 'Erreur lors de la création du référentiel');
        render('admin.layout.php', 'referentiel/list.html.php', [
            'user' => $user,
            'name' => $name,
            'description' => $description
        ]);
        return;
    }
    
    // Si une promotion a été sélectionnée, affecter le référentiel
    if (!empty($promotion)) {
        $model['assign_referentiels_to_promotion']($promotion, [$result]);
    }
    
    // Redirection avec message de succès
    $session_services['set_flash_message']('success', 'Référentiel créé avec succès');
    redirect('?page=referentiels');
}

// Affichage du formulaire d'affectation de référentiels à une promotion
function assign_referentiels_form() {
    global $model, $session_services;
    
    // Vérification des droits d'accès (Admin uniquement)
    $user = check_profile(Enums\ADMIN);
    
    // Récupération de la promotion courante
    $current_promotion = $model['get_current_promotion']();
    
    if (!$current_promotion) {
        $session_services['set_flash_message']('info', 'Aucune promotion active. Veuillez d\'abord activer une promotion.');
        redirect('?page=promotions');
        return;
    }
    
    // Récupération de tous les référentiels
    $all_referentiels = $model['get_all_referentiels']();
    
    // Récupération des référentiels déjà affectés à la promotion
    $assigned_referentiels = $model['get_referentiels_by_promotion']($current_promotion['id']);
    $assigned_ids = array_map(function($ref) {
        return $ref['id'];
    }, $assigned_referentiels);
    
    // Filtrer les référentiels non affectés
    $unassigned_referentiels = array_filter($all_referentiels, function($ref) use ($assigned_ids) {
        return !in_array($ref['id'], $assigned_ids);
    });
    
    // Affichage de la vue
    render('admin.layout.php', 'referentiel/assign.html.php', [
        'user' => $user,
        'current_promotion' => $current_promotion,
        'unassigned_referentiels' => array_values($unassigned_referentiels)
    ]);
}

// Traitement de l'affectation de référentiels à une promotion
function assign_referentiels_process() {
    global $model, $session_services, $error_messages, $success_messages;
    
    // Vérification des droits d'accès (Admin uniquement)
    check_profile(Enums\ADMIN);
    
    // Récupération de la promotion courante
    $current_promotion = $model['get_current_promotion']();
    
    if (!$current_promotion) {
        $session_services['set_flash_message']('info', 'Aucune promotion active. Veuillez d\'abord activer une promotion.');
        redirect('?page=promotions');
        return;
    }
    
    // Récupération des référentiels sélectionnés
    $selected_referentiels = $_POST['referentiels'] ?? [];
    
    if (empty($selected_referentiels)) {
        $session_services['set_flash_message']('info', 'Aucun référentiel sélectionné.');
        redirect('?page=assign-referentiels');
        return;
    }
    
    // Affectation des référentiels à la promotion
    $result = $model['assign_referentiels_to_promotion']($current_promotion['id'], $selected_referentiels);
    
    if (!$result) {
        $session_services['set_flash_message']('danger', 'Erreur lors de l\'assignation des référentiels');
        redirect('?page=assign-referentiels');
        return;
    }
    
    // Redirection vers la liste des référentiels de la promotion avec un message de succès
    $session_services['set_flash_message']('success', 'Les référentiels ont été assignés avec succès');
    redirect('?page=referentiels');
}

function assign_referentiels_to_promotion() {
    global $model, $session_services;
    
    $promotion_id = $_POST['promotion_id'] ?? null;
    $referentiel_ids = $_POST['referentiel_ids'] ?? [];
    
    if (!$promotion_id || !is_array($referentiel_ids)) {
        $session_services['set_flash_message']('error', 'Données invalides');
        redirect('?page=referentiels');
        return;
    }
    
    $result = $model['assign_referentiels_to_promotion']($promotion_id, $referentiel_ids);
    
    if ($result) {
        $session_services['set_flash_message']('success', 'Référentiels assignés avec succès');
    } else {
        $session_services['set_flash_message']('error', 'Erreur lors de l\'assignation');
    }
    
    redirect('?page=referentiels');
}

/**
 * Affiche le formulaire de création d'un référentiel
 */
function create_referentiel_page() {
    global $model, $session_services;
    
    // Vérification des droits d'accès
    $user = check_auth();
    
    // Vérification du rôle administrateur
    check_profile(Enums\ADMIN);
    
    // Affichage de la vue
    render('admin.layout.php', 'referentiel/create.html.php', [
        'user' => $user,
        'active_menu' => 'referentiels'
    ]);
}

/**
 * Traite la soumission du formulaire de création d'un référentiel
 */
function create_referentiel_process() {
    global $model, $session_services;
    
    // Vérification des droits d'accès
    $user = check_auth();
    check_profile(Enums\ADMIN);
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $session_services['set_flash_message']('danger', 'Méthode non autorisée');
        redirect('?page=referentiels');
        return;
    }
    
    // Récupération et validation des données
    $name = $_POST['nom'] ?? '';
    $description = $_POST['description'] ?? '';
    $capacity = intval($_POST['capacite'] ?? 30);
    $sessions = intval($_POST['sessions'] ?? 1);
    
    if (empty($name)) {
        $session_services['set_flash_message']('danger', 'Le nom du référentiel est obligatoire');
        redirect('?page=create-referentiel');
        return;
    }
    
    // Traitement de l'image
    $image_path = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = Enums\UPLOAD_DIR;
        $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $new_filename = uniqid() . '.' . $file_extension;
        $upload_path = $upload_dir . '/' . $new_filename;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
            $image_path = '/assets/images/uploads/' . $new_filename;
        }
    }
    
    // Création du référentiel avec une structure uniformisée
    $result = $model['create_referentiel']([
        'name' => $name,
        'description' => $description,
        'capacite' => $capacity,
        'sessions' => $sessions,
        'image' => $image_path ?? 'assets/images/uploads/promotions/default.png',
        'status' => 'active',
        'modules' => [] // Ajout d'un tableau vide pour les modules
    ]);
    
    if ($result) {
        $session_services['set_flash_message']('success', 'Référentiel créé avec succès');
        redirect('?page=referentiels');
    } else {
        $session_services['set_flash_message']('danger', 'Erreur lors de la création du référentiel');
        redirect('?page=create-referentiel');
    }
}