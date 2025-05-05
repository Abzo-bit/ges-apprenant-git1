<div class="container">
    <div class="header">
        <div class="header-title">
            <h1>Ajouter une promotion</h1>
            <div class="header-subtitle">Créer une nouvelle promotion</div>
        </div>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $error): ?>
                <p><?= $error ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form action="?page=add-promotion-process" method="POST" enctype="multipart/form-data" class="promotion-form">
        <div class="form-group">
            <label for="promotion-name">Nom de la promotion</label>
            <input type="text" id="promotion-name" name="name" 
                   value="<?= htmlspecialchars($formData['name'] ?? '') ?>"
                   placeholder="Entrez le nom de la promotion">
            <?php if (isset($fieldErrors['name'])): ?>
                <div class="error-message"><?= $fieldErrors['name'] ?></div>
            <?php endif; ?>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="start-date">Date de début</label>
                <div class="date-input-container">
                    <input type="text" id="start-date" name="date_debut" 
                           value="<?= htmlspecialchars($formData['date_debut'] ?? '') ?>"
                           placeholder="JJ/MM/AAAA">
                    <span class="calendar-icon">📅</span>
                </div>
                <?php if (isset($fieldErrors['date_debut'])): ?>
                    <div class="error-message"><?= $fieldErrors['date_debut'] ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="end-date">Date de fin</label>
                <div class="date-input-container">
                    <input type="text" id="end-date" name="date_fin" 
                           value="<?= htmlspecialchars($formData['date_fin'] ?? '') ?>"
                           placeholder="JJ/MM/AAAA">
                    <span class="calendar-icon">📅</span>
                </div>
                <?php if (isset($fieldErrors['date_fin'])): ?>
                    <div class="error-message"><?= $fieldErrors['date_fin'] ?></div>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-group">
            <label>Image de la promotion</label>
            <div class="file-upload-container">
                <input type="file" name="image" id="image" accept="image/*" style="display: none;">
                <button type="button" class="upload-button" onclick="document.getElementById('image').click()">
                    Choisir une image
                </button>
                <span class="upload-text">ou glissez-déposez votre image ici</span>
                <?php if (isset($fieldErrors['image'])): ?>
                    <div class="error-message"><?= $fieldErrors['image'] ?></div>
                <?php endif; ?>
                <div class="file-restrictions">Formats acceptés : JPEG, PNG, GIF (max 2MB)</div>
            </div>
        </div>

        <div class="form-group">
            <label>Référentiels</label>
            <div class="referentiels-container">
                <?php foreach ($referentiels as $ref): ?>
                    <div class="referentiel-item">
                        <input type="checkbox" name="referentiels[]" 
                               value="<?= $ref['id'] ?>"
                               id="ref_<?= $ref['id'] ?>"
                               <?= in_array($ref['id'], $formData['referentiels'] ?? []) ? 'checked' : '' ?>>
                        <label for="ref_<?= $ref['id'] ?>"><?= htmlspecialchars($ref['name']) ?></label>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="form-buttons">
            <button type="button" class="cancel-button" onclick="window.location.href='?page=promotions'">
                Annuler
            </button>
            <button type="submit" class="submit-button">
                Créer la promotion
            </button>
        </div>
    </form>
</div>