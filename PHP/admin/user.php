<?php
include "../model/Database.php";
include "../model/Member.php";
include "../model/Concept.php";
include "../model/Lang.php";

$pdo = Database::startSession();

if (!isset($_SESSION['ISADMIN'])) {
    header("Location: ../concept/concepts.php");
    exit();
}

if (isset($_GET['member_id']) && !empty($_GET['member_id'])) {
    if (Member::isExistOnlyMemberWithOnlyIdentifier($pdo, intval($_GET['member_id']))) {
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
        header("Location: user.php");
        exit();
    }
}

if (isset($_GET['modify']) && !empty($_GET['modify'])) {
    $member = intval($_GET['modify']);
    $not_hash = Member::getAleaPassword();

    $pass = hash('sha256', $not_hash);
    Member::updateMember($pdo, intval($_GET['modify']), array(array('MDPMEM', $pass)));
    $file = fopen("../../administrator/log/user.log", "ab+");
    fwrite($file, "Modify_Pass_User : " . Member::getAllFieldsOfMemberWithIdentifiant($pdo, $member)['PSEUDOMEM'] . " - " . $not_hash . "\n");
    header("Location: ../admin/user.php");
    exit();
}

$members = Member::getAllMember($pdo);
// print_r($members);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion de la plateforme</title>
    <link rel="stylesheet" href="../../CSS/general/menu.css">
    <link rel="stylesheet" href="../../CSS/general/all.css">
    <link rel="stylesheet" href="../../CSS/admin/gestionning.css">
</head>
<body>
    <header>
        <?php include "../general/menu.php"; ?>
        <script> clickedItem("admin") </script>
    </header>
    <?php include "sub_menu.php"; ?>

    <div id="adding">
        <a class="clickable" href="../auth/register.php">Ajouter un utilisateur</a>
    </div>

    <div id="information">
        <table>
            <thead>
                <tr>
                    <th>Identifiant</th>
                    <th>Prénom</th>
                    <th>Nom de famille</th>
                    <th>Adresse mail</th>
                    <th>Pseudo</th>
                    <th>Changer le mot de passe</th>
                    <th>Supprimer</th>
                </tr>
            </thead>
            <tbody>
                <?php for ($i = 0; $i < count($members); $i++) { ?>
                    <tr>
                        <?php for ($index = 0; $index < count($members[$i]); $index++) { ?>
                            <td><?php echo $members[$i][$index]; ?></td>
                        <?php } ?>
                        <td onclick="modifyPassword(<?php echo intval($members[$i][0]); ?>)"><img src="../../assets/icons/private.png" alt="Icone de changement de mot de passe"></td>
                        <td class="remove" onclick="removeUser(<?php echo intval($members[$i][0]); ?>)"><img src="../../assets/icons/remove.png" alt="Icone de suppresion"></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <script src="../../JS/icons_for_gestionning.js"></script>
</body>
</html>
