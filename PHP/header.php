<?php
// Connexion à la base de données
try {
    $bdd = new PDO('mysql:host=localhost;dbname=monitoring_iot;charset=utf8', 'root', '');
} catch (Exception $e) {
    // En cas d'erreur de connexion, affiche un message d'erreur
    die('Error : ' . $e->getMessage());
}

// Fonction pour définir la couleur en fonction du statut
function statusColor($status){
    if ($status === "ON"){
        return "green-text";
    } else if ($status === "OFF"){
        return "red-text";
    } else if ($status === "ERREUR"){
        return "yellow-text";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    