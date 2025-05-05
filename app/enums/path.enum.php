<?php

namespace App\Enums;

// Chemins des vues
const VIEW_PATH = 'app/views/';
const LAYOUT_PATH = 'app/views/layout/';
const AUTH_VIEW_PATH = 'app/views/auth/';
const PROMOTION_VIEW_PATH = 'app/views/promotion/';
const REFERENTIEL_VIEW_PATH = 'app/views/referentiel/';
const ERROR_PATH = 'app/views/error/';

// Chemins des données et assets
const DATA_PATH = __DIR__ . '/../../app/data/data.json';
const ASSETS_PATH = __DIR__ . '/../../public/assets/';
const UPLOAD_PATH = __DIR__ . '/../../public/assets/images/uploads/';
const UPLOAD_DIR = __DIR__ . '/../../public/assets/images/uploads';

// Vérifier et créer les dossiers si nécessaire
if (!is_dir(dirname(DATA_PATH))) {
    mkdir(dirname(DATA_PATH), 0775, true);
}

if (!is_dir(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0775, true);
}