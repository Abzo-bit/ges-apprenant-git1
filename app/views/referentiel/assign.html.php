<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assigner des référentiels</title>
    <style>
        /* Reset et styles généraux */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Arial, sans-serif;
        }

        body {
            background-color: #f5f7fa;
            color: #333;
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            /* margin: 0 auto;
            gap: 20px; */
        }

        /* Header */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .header h1 {
            font-size: 24px;
            font-weight: 600;
            color: #2c3e50;
        }

        /* Boutons */
        .btn {
            padding: 10px 20px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
        }

        .btn-back {
            background-color:rgb(21, 88, 97);
            color: white;
        }

        .btn-back:hover {
            background-color: #5a6268;
        }

        .btn-teal {
            background-color: #0E8F7E;
            color: white;
        }

        .btn-teal:hover {
            background-color: #0c745f;
        }

        /* Search section */
        .search-section {
            margin-bottom: 30px;
        }

        .search-bar {
            position: relative;
            width: 100%;
            max-width: 600px;
        }

        .search-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
        }

        .search-bar input {
            width: 100%;
            padding: 12px 15px 12px 45px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .search-bar input:focus {
            outline: none;
            border-color: #0E8F7E;
            box-shadow: 0 0 0 3px rgba(14, 143, 126, 0.1);
        }

        /* Cards container */
        .cards-container {
            width: 100%;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 26px;
            margin-bottom: 20px;

        }

        .card {
            width: 300px;
            height: 400px;
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            margin-right: 20px;

        }

        .card-image {
            width: 100%;
            height: 200px;
            overflow: hidden;
        }

        .card-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .card-content {
            padding: 20px;
            margin-right: 20px;

        }

        .card-title {
            font-size: 18px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 8px;
        }

        .card-subtitle {
            font-size: 14px;
            color: #0E8F7E;
            margin-bottom: 10px;
        }

        .card-description {
            font-size: 14px;
            color: #666;
            margin-bottom: 15px;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* Checkbox styling */
        .form-check {
            display: flex;
            align-items: center;
            margin-top: 15px;
        }

        .form-check input[type="checkbox"] {
            width: 18px;
            height: 18px;
            margin-right: 10px;
            accent-color: #0E8F7E;
            cursor: pointer;
        }

        .form-check label {
            font-size: 14px;
            cursor: pointer;
            user-select: none;
        }

        /* Action buttons */
        .action-buttons {
            display: flex;
            justify-content: flex-end;
            margin-top: 30px;
        }

        /* No data message */
        .no-data {
            text-align: center;
            padding: 40px;
            font-size: 16px;
            color: #666;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }

            .search-bar {
                max-width: 100%;
            }

            .cards-container {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            }
        }

        @media (max-width: 480px) {
            .container {
                padding: 10px;
            }

            .header h1 {
                font-size: 20px;
            }

            .cards-container {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Assigner des référentiels à <?= htmlspecialchars($current_promotion['name']) ?></h1>
            <a href="?page=referentiels" class="btn btn-back">Retour</a>
        </div>

        <form action="?page=assign-referentiels-process" method="POST">
            <div class="search-section">
                <div class="search-bar">
                    <div class="search-icon">🔍</div>
                    <input type="text" id="search" name="search" placeholder="Rechercher un référentiel...">
                </div>
            </div>

            <div class="cards-container">
                <?php if (empty($unassigned_referentiels)): ?>
                    <div class="no-data">Aucun référentiel disponible pour l'assignation</div>
                <?php else: ?>
                    <?php foreach ($unassigned_referentiels as $ref): ?>
                        <div class="card">
                            <div class="card-image">
                                <img src="<?= $ref['image'] ?? 'assets/images/referentiels/default.jpg' ?>" 
                                     alt="<?= htmlspecialchars($ref['name']) ?>">
                            </div>
                            <div class="card-content">
                                <h3 class="card-title"><?= htmlspecialchars($ref['name']) ?></h3>
                                <p class="card-subtitle"><?= count($ref['modules'] ?? []) ?> modules</p>
                                <p class="card-description"><?= htmlspecialchars($ref['description']) ?></p>
                                <div class="form-check">
                                    <input type="checkbox" name="referentiels[]" value="<?= $ref['id'] ?>" 
                                           id="ref_<?= $ref['id'] ?>">
                                    <label for="ref_<?= $ref['id'] ?>">Sélectionner</label>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <?php if (!empty($unassigned_referentiels)): ?>
                <div class="action-buttons">
                    <button type="submit" class="btn btn-teal">
                        <span>✓</span> Assigner les référentiels sélectionnés
                    </button>
                </div>
            <?php endif; ?>
        </form>
    </div>
</body>
</html>