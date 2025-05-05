<?php

namespace App\Controllers;

require_once __DIR__ . '/controller.php';
require_once __DIR__ . '/../models/model.php';
require_once __DIR__ . '/../services/validator.service.php';
require_once __DIR__ . '/../services/session.service.php';
require_once __DIR__ . '/../services/file.service.php';
require_once __DIR__ . '/../translate/fr/error.fr.php';
require_once __DIR__ . '/../translate/fr/message.fr.php';
require_once __DIR__ . '/../enums/profile.enum.php';
require_once __DIR__ . '/../enums/status.enum.php'; // Ajout de cette ligne
require_once __DIR__ . '/../enums/messages.enum.php';

use App\Models;
use App\Services;
use App\Translate\fr;
use App\Enums;
use App\Enums\Status; // Ajout de cette ligne
use App\Enums\Messages;

// Affichage de la liste des promotions
function list_promotions() {
    global $model, $session_services;
    
    // Récupérer les paramètres de pagination et de vue
    $items_per_page = 8; // Définir le nombre d'éléments par page
    $current_page = isset($_GET['page_num']) ? max(1, (int)$_GET['page_num']) : 1;
    $current_view = $_GET['view'] ?? 'grid';
    $search = $_GET['search'] ?? '';
    
    // Vérification de l'authentification
    $user = check_auth();
    
    // Récupérer les statistiques
    $stats = $model['get_statistics']();
    
    // Récupérer et filtrer les promotions
    $promotions = $model['get_all_promotions']();
    
    // Récupérer le statut du filtre
    $status_filter = $_GET['status'] ?? null;
    
    // Filtrer les promotions selon le statut si nécessaire
    if ($status_filter) {
        $promotions = array_filter($promotions, function($promotion) use ($status_filter) {
            return $promotion['status'] === $status_filter;
        });
        $promotions = array_values($promotions); // Réindexer le tableau
    }
    
    if (!empty($search)) {
        $promotions = array_filter($promotions, function($promotion) use ($search) {
            return stripos($promotion['name'], $search) !== false;
        });
        $promotions = array_values($promotions);
    }

    // Séparation et tri des promotions pour chaque page
    $items_per_page = 8;
    $current_page = isset($_GET['page_num']) ? max(1, (int)$_GET['page_num']) : 1;

    // 1. Séparer les promotions actives et inactives
    $active_promotions = array_filter($promotions, function($promo) {
        return $promo['status'] === 'active';
    });
    $inactive_promotions = array_filter($promotions, function($promo) {
        return $promo['status'] !== 'active';
    });

    // 2. Trier chaque groupe par date
    $sort_by_date = function($a, $b) {
        return strtotime($b['date_debut']) - strtotime($a['date_debut']);
    };

    usort($active_promotions, $sort_by_date);
    usort($inactive_promotions, $sort_by_date);

    // 3. Calculer les indices pour la pagination
    $total_items = count($promotions);
    $total_pages = ceil($total_items / $items_per_page);
    $current_page = min($current_page, $total_pages);
    
    // 4. Répartir les promotions sur la page courante
    $start_index = ($current_page - 1) * $items_per_page;
    $paginated_promotions = [];
    
    // Ajouter les promotions actives de la page courante
    foreach ($active_promotions as $active_promo) {
        if (count($paginated_promotions) < $items_per_page) {
            $paginated_promotions[] = $active_promo;
        }
    }

    // Compléter avec les promotions inactives
    $remaining_slots = $items_per_page - count($paginated_promotions);
    if ($remaining_slots > 0) {
        $inactive_slice = array_slice($inactive_promotions, 0, $remaining_slots);
        $paginated_promotions = array_merge($paginated_promotions, $inactive_slice);
    }

    // Récupérer la promotion active
    $active_promotion = get_active_promotion();

    // Rendu de la vue
    render('admin.layout.php', 'promotion/list.html.php', [
        'user' => $user,
        'promotions' => $paginated_promotions,
        'current_view' => $current_view,
        'search' => $search,
        'active_menu' => 'promotions',
        'current_page' => $current_page,
        'total_pages' => $total_pages,
        'items_per_page' => $items_per_page,
        'total_items' => $total_items,
        'active_promotion' => $active_promotion, // Ajouter cette ligne
        'stats' => $stats
    ]);
}

// Fonction pour afficher le formulaire d'ajout de promotion
function add_promotion() {
    // Récupérer la liste des référentiels pour les checkboxes
    $referentiels = get_all_referentiels();
    
    // Récupérer les données du formulaire en cas d'erreur (pour repopuler le formulaire)
    $formData = $_SESSION['form_data'] ?? [];
    $errors = $_SESSION['form_errors'] ?? [];
    $fieldErrors = $_SESSION['field_errors'] ?? [];
    
    // Nettoyer les données de session après utilisation
    unset($_SESSION['form_data']);
    unset($_SESSION['form_errors']);
    unset($_SESSION['field_errors']);
    
    // Afficher la vue en passant les paramètres correctement
    render('admin.layout.php', 'promotion/add.html.php', [
        'referentiels' => $referentiels,
        'formData' => $formData,
        'errors' => $errors,
        'fieldErrors' => $fieldErrors
    ]);
}

/**
 * Récupère tous les référentiels
 */
function get_all_referentiels() {
    // Cette fonction va chercher les données dans data.json
    $data = json_decode(file_get_contents(dirname(__DIR__) . '/data/data.json'), true);
    return $data['referentiels'] ?? [];
}



// Fonction pour traiter le formulaire d'ajout de promotion
function add_promotion_process() {
    global $model;

    $errors = [];
    $fieldErrors = [];
    
    // Récupérer et valider les données du formulaire
    $name = $_POST['name'] ?? '';
    $date_debut = $_POST['date_debut'] ?? '';
    $date_fin = $_POST['date_fin'] ?? '';
    $referentiels = $_POST['referentiels'] ?? [];
    
    // Validation du nom
    if (empty(trim($name))) {
        $fieldErrors['name'] = 'Le nom de la promotion est requis';
    } elseif (strlen(trim($name)) < 3) {
        $fieldErrors['name'] = 'Le nom doit contenir au moins 3 caractères';
    }
    
    // Validation de la date de début
    if (empty(trim($date_debut))) {
        $fieldErrors['date_debut'] = 'La date de début est requise';
    } elseif (!validate_date_format($date_debut)) {
        $fieldErrors['date_debut'] = 'Format de date invalide (JJ/MM/AAAA)';
    }
    
    // Validation de la date de fin
    if (empty(trim($date_fin))) {
        $fieldErrors['date_fin'] = 'La date de fin est requise';
    } elseif (!validate_date_format($date_fin)) {
        $fieldErrors['date_fin'] = 'Format de date invalide (JJ/MM/AAAA)';
    } elseif (validate_date_format($date_debut) && validate_date_format($date_fin)) {
        // Convertir les dates pour comparaison
        $debut = convert_date_to_db_format($date_debut);
        $fin = convert_date_to_db_format($date_fin);
        
        if (strtotime($fin) <= strtotime($debut)) {
            $fieldErrors['date_fin'] = 'La date de fin doit être postérieure à la date de début';
        }
    }
    
    // Validation de l'image
    $image = $_FILES['image'] ?? null;
    $image_name = '';
    
    if (!empty($image['name'])) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $max_size = 2 * 1024 * 1024; // 2MB
        
        if (!in_array($image['type'], $allowed_types)) {
            $fieldErrors['image'] = 'Le fichier doit être une image (JPEG, PNG, GIF)';
        } elseif ($image['size'] > $max_size) {
            $fieldErrors['image'] = 'L\'image ne doit pas dépasser 2MB';
        } else {
            // Générer un nom unique pour l'image
            $image_name = uniqid() . '_' . $image['name'];
        }
    } else {
        // Image par défaut si aucune n'est fournie
        $image_name = 'default.png';
    }
    
    // Si des erreurs sont présentes, rediriger vers le formulaire avec les erreurs
    if (!empty($fieldErrors)) {
        $_SESSION['field_errors'] = $fieldErrors;
        $_SESSION['form_errors'] = ['Veuillez corriger les erreurs dans le formulaire.'];
        $_SESSION['form_data'] = [
            'name' => $name,
            'date_debut' => $date_debut,
            'date_fin' => $date_fin,
            'referentiels' => $referentiels
        ];
        
        redirect('?page=add-promotion');
        return;
    }
    
    // Convertir les dates au format de la base de données (YYYY-MM-DD)
    $date_debut_db = convert_date_to_db_format($date_debut);
    $date_fin_db = convert_date_to_db_format($date_fin);
    
    // Télécharger l'image si elle existe
    if (!empty($image['name'])) {
        $upload_dir = dirname(dirname(__DIR__)) . '/public/assets/images/uploads/promotions/';
        
        // Créer le dossier s'il n'existe pas
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        // Nettoyer le nom du fichier
        $image_name = preg_replace('/[^a-zA-Z0-9._-]/', '', $image_name);
        
        // Chemin complet du fichier
        $image_path = $upload_dir . $image_name;
        
        // Tenter de déplacer le fichier
        if (!move_uploaded_file($image['tmp_name'], $image_path)) {
            $_SESSION['form_errors'] = ['Erreur lors du téléchargement de l\'image.'];
            $_SESSION['form_data'] = [
                'name' => $name,
                'date_debut' => $date_debut,
                'date_fin' => $date_fin,
                'referentiels' => $referentiels
            ];
            redirect('?page=add-promotion');
            return;
        }
    }
    
    // Créer la nouvelle promotion
    $new_promotion = [
        'name' => $name,
        'date_debut' => $date_debut_db,
        'date_fin' => $date_fin_db,
        'image' => $image_name,
        'status' => 'inactive',
        'apprenants' => [],
        'referentiels' => $referentiels
    ];
    
    // Ajouter la promotion à la base de données
    $result = $model["add_promotion_to_db"] ($new_promotion);
    
    if ($result) {
        // Rediriger vers la liste des promotions avec un message de succès
        $_SESSION['flash'] = [
            'type' => 'success',
            'message' => 'La promotion a été ajoutée avec succès.'
        ];
        redirect('?page=promotions');
    } else {
        // En cas d'erreur, rediriger vers le formulaire avec un message d'erreur
        $_SESSION['form_errors'] = ['Une erreur est survenue lors de l\'ajout de la promotion.'];
        $_SESSION['form_data'] = [
            'name' => $name,
            'date_debut' => $date_debut,
            'date_fin' => $date_fin,
            'referentiels' => $referentiels
        ];
        
        redirect('?page=add-promotion');
    }
}

// Fonction utilitaire pour valider le format de date JJ/MM/AAAA
function validate_date_format($date) {
    if (!preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $date, $matches)) {
        return false;
    }
    
    $day = (int)$matches[1];
    $month = (int)$matches[2];
    $year = (int)$matches[3];
    
    return checkdate($month, $day, $year);
}

//Fonction utilitaire pour convertir une date du format JJ/MM/AAAA au format YYYY-MM-DD
function convert_date_to_db_format($date) {
    if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $date, $matches)) {
        return $matches[3] . '-' . $matches[2] . '-' . $matches[1];
    }
    return $date;
}



// Modification du statut d'une promotion (activation/désactivation)
function toggle_promotion_status() {
    global $model, $session_services;
    
    // Vérification de l'authentification
    check_auth();
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirect('?page=promotions');
        return;
    }
    
    $promotion_id = filter_input(INPUT_POST, 'promotion_id', FILTER_VALIDATE_INT);
    if (!$promotion_id) {
        $session_services['set_flash_message']('error', Messages::PROMOTION_ERROR->value);
        redirect('?page=promotions');
        return;
    }
    
    $result = $model['toggle_promotion_status']($promotion_id);
    
    if ($result) {
        $message = $result['status'] === Status::ACTIVE->value ? 
                  Messages::PROMOTION_ACTIVATED->value : 
                  Messages::PROMOTION_INACTIVE->value;
        $session_services['set_flash_message']('success', $message);
    } else {
        $session_services['set_flash_message']('error', Messages::PROMOTION_ERROR->value);
    }
    
    redirect('?page=promotions');
}

function toggle_promotion() {
    global $model;
    
    $promotion_id = $_POST['promotion_id'] ?? null;
    
    if (!$promotion_id) {
        $_SESSION['error'] = "ID de promotion manquant";
        redirect('?page=promotions');
        return;
    }
    
    // Récupérer la promotion
    $promotion = $model['get_promotion_by_id']($promotion_id);
    
    if (!$promotion) {
        $_SESSION['error'] = "Promotion non trouvée";
        redirect('?page=promotions');
        return;
    }
    
    // Si la promotion est déjà active, ne pas permettre la désactivation
    if ($promotion['status'] === 'active') {
        $_SESSION['error'] = "Impossible de désactiver une promotion active";
        redirect('?page=promotions');
        return;
    }
    
    // Sinon, activer la promotion
    $model['toggle_promotion_status']($promotion_id);
    
    redirect('?page=promotions');
}

function get_active_promotion() {
    global $model;
    $promotions = $model['get_all_promotions']();
    $active_promotion = null;
    
    foreach ($promotions as $promotion) {
        if ($promotion['status'] === 'active') {
            $active_promotion = $promotion;
            break;
        }
    }
    
    return $active_promotion;
}

function promotion_page() {
    global $model, $session_services;
    
    // Vérification des droits d'accès
    $user = check_auth();
    
    // Récupération de l'ID de la promotion depuis l'URL
    $promotion_id = $_GET['id'] ?? null;
    
    if (!$promotion_id) {
        $session_services['set_flash_message']('danger', 'Identifiant de promotion manquant');
        redirect('?page=promotions');
        return;
    }
    
    // Récupération des détails de la promotion
    $promotion = $model['get_promotion_by_id']($promotion_id);
    
    if (!$promotion) {
        $session_services['set_flash_message']('danger', 'Promotion non trouvée');
        redirect('?page=promotions');
        return;
    }
    
    // Récupération des référentiels associés à la promotion
    $referentiels = $model['get_referentiels_by_promotion']($promotion_id);
    
    // Affichage de la vue
    render('admin.layout.php', 'promotion/view.html.php', [
        'user' => $user,
        'promotion' => $promotion,
        'referentiels' => $referentiels,
        'active_menu' => 'promotions'
    ]);
}

// Ajout d'une promotion
// function add_promotion() {
//     global $model, $session_services, $validator_services, $file_services;
    
//     // Vérification de l'authentification
//     $user = check_auth();
    
//     // Vérification de la méthode POST
//     if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
//         $session_services['set_flash_message']('error', Messages::INVALID_REQUEST->value);
//         redirect('?page=promotions');
//         return;
//     }
    
//     // Validation des données
//     $validation = $validator_services['validate_promotion']($_POST, $_FILES);
    
//     if (!$validation['valid']) {
//         $session_services['set_flash_message']('error', $validation['errors'][0]);
//         redirect('?page=promotions');
//         return;
//     }
    
//     // Traitement de l'image avec le service
//     $image_path = $file_services['handle_promotion_image']($_FILES['image']);
//     if (!$image_path) {
//         $session_services['set_flash_message']('error', Messages::IMAGE_UPLOAD_ERROR->value);
//         redirect('?page=promotions');
//         return;
//     }
    
//     // Préparation des données
//     $promotion_data = [
//         'name' => htmlspecialchars($_POST['name']),
//         'date_debut' => $_POST['date_debut'],
//         'date_fin' => $_POST['date_fin'],
//         'image' => $image_path,
//         'status' => 'inactive',
//         'apprenants' => []
//     ];
    
//     // Création de la promotion
//     $result = $model['create_promotion']($promotion_data);
    
//     if (!$result) {
//         $session_services['set_flash_message']('error', Messages::PROMOTION_CREATE_ERROR->value);
//         redirect('?page=promotions');
//         return;
//     }

//     $session_services['set_flash_message']('success', Messages::PROMOTION_CREATED->value);
//     redirect('?page=promotions');
// }

// // Recherche des référentiels
// function search_referentiels() {
//     global $model;
    
//     // Vérification si l'utilisateur est connecté
//     check_auth();
    
//     $query = $_GET['q'] ?? '';
//     $referentiels = $model['search_referentiels']($query);
    
//     // Retourner les résultats en JSON
//     header('Content-Type: application/json');
//     echo json_encode(array_values($referentiels));
//     exit;
// }

// // Affichage de la page de promotion
