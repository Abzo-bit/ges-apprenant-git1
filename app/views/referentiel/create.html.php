<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un Référentiel</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="container">
        <div class="header-container">
            <div class="page-header">
                <a href="?page=referentiels" class="back-link">
                    <i class="fas fa-arrow-left"></i> Retour aux référentiels
                </a>
                <h1>Créer un nouveau référentiel</h1>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <form action="?page=create-referentiel-process" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <div class="upload-zone">
                            <i class="fas fa-cloud-upload-alt upload-icon"></i>
                            <label for="image">Image du référentiel*</label>
                            <input type="file" 
                                   id="image" 
                                   name="image" 
                                   class="form-control" 
                                   accept="image/jpeg,image/png">
                            <p class="help-text">Format accepté : JPG, PNG (max 2MB)</p>
                            <?php if (isset($_SESSION['errors']['image'])): ?>
                                <p class="error-text"><?= $_SESSION['errors']['image'] ?></p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="nom">Nom du référentiel*</label>
                        <input type="text" 
                               id="nom" 
                               name="nom" 
                               class="form-control" 
                               maxlength="100"
                               value="<?= htmlspecialchars($_SESSION['old']['nom'] ?? '') ?>">
                        <?php if (isset($_SESSION['errors']['nom'])): ?>
                            <p class="error-text"><?= $_SESSION['errors']['nom'] ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" 
                                name="description" 
                                class="form-control" 
                                rows="4"><?= htmlspecialchars($_SESSION['old']['description'] ?? '') ?></textarea>
                        <?php if (isset($_SESSION['errors']['description'])): ?>
                            <p class="error-text"><?= $_SESSION['errors']['description'] ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="capacite">Capacité (nombre d'étudiants)*</label>
                            <input type="number" 
                                   id="capacite" 
                                   name="capacite" 
                                   class="form-control" 
                                   min="1" 
                                   max="30"
                                   value="<?= htmlspecialchars($_SESSION['old']['capacite'] ?? '30') ?> ">
                            <?php if (isset($_SESSION['errors']['capacite'])): ?>
                                <p class="error-text"><?= $_SESSION['errors']['capacite'] ?></p>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group col-md-6">
                            <label for="sessions">Nombre de sessions*</label>
                            <select id="sessions" name="sessions" class="form-control">
                                <?php 
                                $selected_session = $_SESSION['old']['sessions'] ?? '1';
                                for($i = 1; $i <= 4; $i++): 
                                ?>
                                    <option value="<?= $i ?>" <?= $selected_session == $i ? 'selected' : '' ?>>
                                        <?= $i ?> session<?= $i > 1 ? 's' : '' ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                            <?php if (isset($_SESSION['errors']['sessions'])): ?>
                                <p class="error-text"><?= $_SESSION['errors']['sessions'] ?></p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Créer</button>
                        <a href="?page=referentiels" class="btn btn-secondary">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php
    // Nettoyer les données de session après affichage
    unset($_SESSION['errors']);
    unset($_SESSION['old']);
    ?>
</body>
</html>