<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tous les Référentiels</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <div class="container">
        <div class="header-container">
            <div class="page-header">
                <a href="?page=referentiels" class="back-link">
                    <i class="fas fa-arrow-left"></i> Retour aux référentiels actifs
                </a>
                <h1>Tous les Référentiels</h1>
                <p class="subtitle">Liste complète des référentiels de formation</p>
            </div>
            
            <div class="search-container">
                <form action="?page=all-referentiels" method="GET" class="search-form">
                    <input type="hidden" name="page" value="all-referentiels">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input 
                            type="text" 
                            name="search" 
                            placeholder="Rechercher un référentiel..." 
                            class="search-input"
                            value="<?= htmlspecialchars($search ?? '') ?>"
                        >
                    </div>
                    <button type="submit" class="btn btn-secondary">
                        <i class="fas fa-search"></i> Rechercher
                    </button>
                </form>
                <a href="?page=create-referentiel" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Créer un référentiel
                </a>
            </div>
        </div>

        <div class="referentiels-grid">
            <?php if (!empty($referentiels)): ?>
                <?php foreach ($referentiels as $ref): ?>
                    <div class="referentiel-card">
                        <div class="card-image">
                            <img src="<?= $ref['image'] ?? '/assets/images/referentiels/default.jpg' ?>" 
                                 alt="<?= htmlspecialchars($ref['name']) ?>">
                        </div>
                        <div class="card-content">
                            <h3><?= htmlspecialchars($ref['name']) ?></h3>
                            <p class="description"><?= htmlspecialchars($ref['description']) ?></p>
                            <div class="card-footer">
                                <div class="capacity">
                                    Capacité: <?= $ref['capacity'] ?? '25' ?> places
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-data">
                    <p>Aucun référentiel trouvé</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Ajout des icônes Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</body>
</html>