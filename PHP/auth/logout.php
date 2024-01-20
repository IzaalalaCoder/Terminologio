<?php

/**
 * La page logout.php permet la déconnexion d'un membre
 * de la plateforme.
 */

// Test de la presence d'une session
// Si vrai alors fermeture de la session
// Sinon retour sur la page d'accueil
include "../model/Database.php";

define("TMP_PATH", "../../assets/concepts/tmp/");

$pdo = Database::startSession();
Database::endSession();

$dirs = scandir(TMP_PATH);
foreach ($dirs as $file) {
    if (strcmp($file, '.') == 0 || strcmp($file, '..') == 0) {
        echo "not";
        continue;
    }
    // echo "$file<br>";
    unlink(TMP_PATH . $file);
}

header("Location: ../concept/rem.php?state=upload");
exit();
?>
