<?php
// Inclusion de l'en-tête
require_once('header.php');

// Requête SQL pour rechercher un module et son historique d'opération
$rqSearchModule = $bdd->prepare('SELECT mo.id, mo.name, mo.description, mo.type, mo.location, oh.status, oh.description_event, oh.date_time
FROM modules AS mo
INNER JOIN operating_history AS oh ON mo.id = oh.id_module
INNER JOIN (
    SELECT id_module, MAX(date_time) AS max_date
    FROM operating_history
    GROUP BY id_module
) AS oh_max ON oh.id_module = oh_max.id_module AND oh.date_time = oh_max.max_date
WHERE mo.id = ?;
');

// Exécution de la requête pour obtenir les informations du module
$rqSearchModule->execute([$_GET['id']]);
$module = $rqSearchModule->fetch();

// Requête SQL pour rechercher les données de mesure associées au module
$rqSearchDatasInfo = $bdd->prepare('SELECT measured_data, date_time FROM `data` WHERE id_module = ?
        ORDER BY `date_time` ASC');

// Exécution de la requête pour obtenir les informations sur les données
$rqSearchDatasInfo->execute([$_GET['id']]);
$datasInfo = $rqSearchDatasInfo->fetchAll();

// Définit le fuseau horaire sur 'Europe/Paris'
date_default_timezone_set('Europe/Paris');

// Crée un objet DateTimeImmutable pour la date actuelle & de la date du module
$today = new DateTimeImmutable(date("Y-m-d H:i:s"));
$dateModule = new DateTimeImmutable($module['date_time']);

// Calcule l'intervalle de temps entre la date du module et la date actuelle
$interval = $dateModule->diff($today);

// Détermine la longueur du tableau des informations sur les données du module
$lengthDatasInfos = count($datasInfo);

$typeMapping = [
    "Température" => " °C",
    "Pression" => " Pa",
    "Humidité" => " g/m3",
    "Mouvement" => " km/h",
    "Lumière" => " lux",
    "Son" => " dB",
    "Autre" => "Unité de mesure non renseignée."
];

$unit = $typeMapping[$module["type"]];
?>


<title>IoT Monitor - Détails de <?= $module['name'] ?></title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
<link rel="stylesheet" href="../CSS/details.css">
</head>

<body>
    <header class="d-flex justify-content-center py-3">
        <ul class="nav nav-pills">
            <li class="nav-item"><a href="../index.php" class="nav-link active" aria-current="page">Accueil</a></li>
            <li class="nav-item"><a href="addModule.php" class="nav-link">Ajouter un Module</a></li>
        </ul>
    </header>

    <div class="container">
        <h1>Détails de <?= $module['type'] ?> : <?= $module['name'] ?></h1>

        <?php $lengthDatasInfos > 1 ? require_once('graphData.php') : '' ?>

        <div class="module-summary">

            <div class="details">

                <div class="id">
                    <h2>Id du module :</h2>
                    <p><?= $module['id'] ?></p>
                </div>

                <div class="name">
                    <h2>Nom :</h2>
                    <p><?= $module['name'] ?></p>
                </div>

                <div class="description">
                    <h2>Description :</h2>
                    <p><?= $module['description'] ?></p>
                </div>

                <div class="type">
                    <h2>Type :</h2>
                    <p><?= $module['type'] ?></p>
                </div>

                <div class="location">
                    <h2>Localisation :</h2>
                    <p><?= $module['location'] ?></p>
                </div>

            </div>

            <div class="status-info">

                <div class="status">
                    <h2>Statut :</h2>
                    <!-- Affichage du statut du module avec une classe CSS basée sur la fonction statusColor définie dans header.php -->
                    <p class="<?= statusColor($module["status"]) ?>"><?= $module['status'] ?></p>
                </div>

                <div class="dateTime">
                    <h2><?= $module['status'] ?> pendant :</h2>
                    <p id="dateTime"><?= $interval->format("%a j %H h %I m %S s") ?> </p>
                    <p>(Depuis <?= $module['date_time'] ?>)</p>
                </div>

                <div class="descEvent">
                    <h2>Détail de l'évènement :</h2>
                    <p><?= $module['description_event'] ?></p>
                </div>

                <div class="qtyDataSend">
                    <h2>Nombre de données envoyées :</h2>
                    <p><?= $lengthDatasInfos ?></p>
                </div>

                <div class="lastDataSend">
                    <p><?= $datasInfo ? "<h2>Dernière donnée envoyée :</h2>" . $datasInfo[$lengthDatasInfos - 1]["measured_data"] . $unit : "" ?></p>
                </div>

            </div>
        </div>

    </div>

    <!-- Script pour tracer la courbe du graphique -->
    <?php
    if ($lengthDatasInfos > 1) {

        $dataPoints = array();
        $i = 0;

        foreach ($datasInfo as $data) {
            $i++;
            array_push($dataPoints, ['y' => $data["measured_data"], 'label' => $data["date_time"]]);
        }

    ?>

        <script>
            var dataPoints = <?php echo json_encode($dataPoints); ?>;

            window.onload = function() {

                var chart = new CanvasJS.Chart("chartContainer", {
                    theme: "light2", // "light1", "light2", "dark1", "dark2"
                    animationEnabled: true,
                    zoomEnabled: true,
                    title: {
                        text: "Données mesurées (en<?= $unit ?>)"
                    },
                    data: [{
                        type: "line",
                        dataPoints: dataPoints // Utilisation des données provenant de PHP
                    }]

                });

                chart.render();

            }
        </script>

        <!-- Appel du script de la librairie CanvasJS pour pouvoir tracer le graphique -->
        <script src="https://cdn.canvasjs.com/canvasjs.min.js"></script>

    <?php
    }
    ?>


</body>

</html>