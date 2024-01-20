<?php

/**
 * La page remove.php permet la suppresion d'un membre
 * de la plateforme.
 */

// Test sur l'identité du compte
// Si l'identité se trouve bien dans notre base alors suppression
include "../model/Database.php";
include "../model/Member.php";
include "../model/Concept.php";
include "../model/Lang.php";
$pdo = Database::startSession();

if (isset($_SESSION['ISADMIN'])) {
    // echo "d;d";
    if (isset($_GET['member_id']) && !empty($_GET['member_id'])) {
   
    if (Member::isExistOnlyMemberWithOnlyIdentifier($pdo, intval($_GET['member_id']))) { echo "d;dddd";
            $identifiant = intval($_GET['member_id']);
            // echo $identifiant;

            // Suppression des traductions réalisées
            Concept::removeAllTranslateOfMember($pdo, $identifiant);

            // Suppresion des concepts réalisées par le membre
            Concept::removeAllConceptRealizedByMember($pdo, $identifiant);

            // Suppresion de la liste des concepts favoris
            Concept::removeAllConceptMarkedByMember($pdo, $identifiant);

            // Suppresion de la liste des langues utilisés
            Lang::removeLanguagesOfMember($pdo, $identifiant);

            // Suppresion du membre ainsi que de ses paramètres
            Member::removeMember($pdo, $identifiant);
            header("Location: ../admin/user.php");
            exit();
        }
    }
}

header("Location: ../concept/concepts.php");
exit();

?>
