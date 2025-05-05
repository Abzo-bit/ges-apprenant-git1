<?php

namespace App\Enums;

enum Messages: string {
    case INVALID_REQUEST = 'Requête invalide';
    case IMAGE_UPLOAD_ERROR = 'Erreur lors du téléchargement de l\'image';
    case PROMOTION_ERROR = 'Une erreur est survenue lors de la modification du statut de la promotion';
    case PROMOTION_ACTIVATED = 'Promotion  activée avec succès';
    case PROMOTION_INACTIVE = 'Promotion désactivée avec succès';
    case PROMOTION_CREATE_ERROR = 'Erreur lors de la création de la promotion';
    case PROMOTION_CREATED = 'Promotion  créée avec succès';
}