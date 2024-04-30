<?php
// Inclusion de l'en-tête
require_once('header.php');

// Vérifie si les données du formulaire sont présentes
if (isset($_POST['name']) && isset($_POST['type']) && isset($_POST['location'])) {

    // Prépare et exécute la requête d'insertion dans la table 'modules'
    $rqInsertModule = $bdd->prepare('INSERT INTO `modules`(`name`, `description`, `type`, `location`) VALUES (:name, :description, :type, :location)');
    $rqInsertModule->execute([
        'name' => $_POST['name'],
        'description' => $_POST['description'],
        'type' => $_POST['type'],
        'location' => $_POST['location']
    ]);

    // Prépare la requête d'insertion dans la table 'operating_history'
    $rqInsertStatus = $bdd->prepare('INSERT INTO `operating_history`(`id_module`, `date_time`, `status`, `description_event`) VALUES (:id_module, :date_time, :status, \'Ajout à la base de données\')');

    // Récupère l'ID maximum de la table 'modules'
    $result = $bdd->query("SELECT MAX(id) FROM modules");
    $moduleMaxId = $result->fetch();

    // Définit le fuseau horaire et la date actuelle
    date_default_timezone_set('Europe/Paris');
    $today = date("Y-m-d H:i:s");

    // Exécute la requête d'insertion dans la table 'operating_history'
    $rqInsertStatus->execute([
        'id_module' => $moduleMaxId[0],
        'date_time' => $today,
        'status' => $_POST['status']
    ]);

    header('Location:index.php');
}
?>

<title>IoT Monitor - Ajouter un module</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
<link rel="stylesheet" href="../CSS/addModule.css">
</head>

<body>
    <header class="d-flex justify-content-center py-3">
        <ul class="nav nav-pills">
            <li class="nav-item"><a href="../index.php" class="nav-link">Accueil</a></li>
            <li class="nav-item"><a href="addModule.php" class="nav-link active" aria-current="page">Ajouter un Module</a></li>
        </ul>
    </header>

    <div class="container">

        <h1>Ajouter un module IoT</h1>

        <form action="" method="POST">

            <div class="name"><label for="name">Nom du module :</label><br>
                <input type="text" id="name" name="name" placeholder="Nom" required><br><br>
            </div>

            <div class="description">
                <label for="description">Description :</label><br>
                <textarea id="description" name="description" rows="4" cols="50" placeholder="Brève description du module" required></textarea><br><br>
            </div>

            <div class="type">
                <label for="type">Type de capteur :</label><br>
                <select id="type" name="type">
                    <option value="Température">Température</option>
                    <option value="Pression">Pression</option>
                    <option value="Humidité">Humidité</option>
                    <option value="Mouvement">Mouvement</option>
                    <option value="Lumière">Lumière</option>
                    <option value="Son">Son</option>
                    <option value="Autre">Autre</option>
                </select><br><br>
            </div>

            <div class="type">
                <label for="type">Statut :</label><br>
                <select id="status" name="status">
                    <option value='ON'>On</option>
                    <option value='OFF'>Off</option>
                    <option value='ERREUR'>Erreur</option>
                </select><br><br>
            </div>

            <div class="location">
                <label for="location">Localisation :</label><br>
                <input type="text" id="location" name="location"><br><br>
            </div>

            <input type="submit" value="Ajouter le module">
        </form>

    </div>

</body>

</html>