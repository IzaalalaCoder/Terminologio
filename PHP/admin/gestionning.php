<?php

include "../model/Database.php";
include "../model/Lang.php";
include "../model/Concept.php";
$pdo = Database::startSession();

if (!isset($_SESSION['ISADMIN'])) {
    header("Location: ../concept/concepts.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion de Terminologio</title>
    <link rel="stylesheet" href="../../CSS/general/menu.css">
    <link rel="stylesheet" href="../../CSS/general/all.css">
</head>
<body>
    <header>
        <?php include "../general/menu.php"; ?>
        <script> clickedItem("admin") </script>
    </header>
    <?php include "sub_menu.php"; ?>
</body>
</html>
