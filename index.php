<?php
// Inclusion de l'en-tête
require_once('PHP/header.php');

// Exécution de la requête SQL pour récupérer les informations des modules et leur dernier statut
$result = $bdd->query('SELECT mo.id, mo.name, mo.description, mo.type, mo.location, oh.status
    FROM modules AS mo
    INNER JOIN operating_history AS oh ON mo.id = oh.id_module
    INNER JOIN (
        SELECT id_module, MAX(date_time) AS max_date
        FROM operating_history
        GROUP BY id_module
    ) AS oh_max ON oh.id_module = oh_max.id_module AND oh.date_time = oh_max.max_date
    ORDER BY id ASC
');

// Récupération de toutes les lignes de résultat dans un tableau associatif
$modules = $result->fetchAll();

$moduleName = "";

foreach ($modules as $module) {
    $module["status"] === "ERREUR" ? $moduleName = $moduleName . " " . $module["name"] . "," : null;
}

$moduleName = rtrim($moduleName, ',');
?>


<title>IoT Monitor - Accueil</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
<link rel="stylesheet" href="CSS/index.css">
</head>

<body>
    <header class="d-flex justify-content-center py-3">
        <ul class="nav nav-pills">
            <li class="nav-item"><a href="index.php" class="nav-link active" aria-current="page">Accueil</a></li>
            <li class="nav-item"><a href="PHP/addModule.php" class="nav-link">Ajouter un Module</a></li>
    </header>

    <div class="container">

        <?= $moduleName ? "<div class=\"alert alert-danger\" role=\"alert\"><p id=\"notification-text\"></p></div>" : null ?>

        <table class="table">
            <caption>Modules</caption>
            <thead>
                <tr>
                    <th scope="col">Id</th>
                    <th scope="col">Nom</th>
                    <th scope="col">Description</th>
                    <th scope="col">Type</th>
                    <th scope="col">Localisation</th>
                    <th scope="col">Statut</th>
                    <th scope="col"></th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Boucle foreach pour parcourir chaque module du tableau $modules
                foreach ($modules as $module) {
                ?>
                    <tr>
                        <td><?= $module["id"] ?></td>
                        <td><?= $module["name"] ?></td>
                        <td><?= $module["description"] ?></td>
                        <td><?= $module["type"] ?></td>
                        <td><?= $module["location"] ?></td>

                        <!-- Affichage du statut du module avec une classe CSS basée sur la fonction statusColor définie dans header.php -->
                        <td class="<?= statusColor($module["status"]) ?>"><?= $module["status"] ?></td>

                        <!-- Lien vers la page de détails du module avec son ID -->
                        <td><a href=<?= "PHP/details.php?id=" . $module["id"] ?>>Détails</a></td>
                    </tr>
                <?php
                }
                ?>

            </tbody>
        </table>
    </div>

    <script>
        // Sélection de la notification et du texte
        const notification = document.getElementById('notification');
        const notificationText = document.getElementById('notification-text');

        // Fonction pour afficher la notification
        function showNotification(message) {
            notificationText.innerText = message;
        }
        <?php

        ?>

        showNotification("Module(s) disfonctionnel(s) :<?= $moduleName ?>.");
    </script>
</body>

</html>