<?php

include "../model/Database.php";
include "../model/Fields.php";

$pdo = Database::startSession();

if (!isset($_SESSION['ISADMIN'])) {
    header("Location: ../concept/concepts.php");
    exit();
}

$fields = Fields::getAllFields($pdo);

if (isset($_GET['add']) && $_GET['add'] == 'on') {
    if (isset($_POST['name']) && !empty($_POST['name'])) {
        if (Fields::isNotExistField($pdo, $_POST['name'])) {
            Fields::addFields($pdo, $_POST['name']);
            header('Location: fields.php');
            exit();
        } else {
            $error = "La catégorie existe déjà";
        }
    } else {
        $error = "La saisie du nom de la catégorie est vide";
    }
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion de la plateforme</title>
    <link rel="stylesheet" href="../../CSS/general/menu.css">
    <link rel="stylesheet" href="../../CSS/admin/gestionning.css">
    <link rel="stylesheet" href="../../CSS/general/all.css">
</head>
<body>
    <header>
        <?php include "../general/menu.php"; ?>
        <script> clickedItem("admin") </script>
    </header>
    <?php include "sub_menu.php";
    if (isset($error) && !empty($error)) { ?>
        <script>alert("<?php echo $error; ?>");</script>
    <?php } ?>

    <fieldset>
        <legend>Ajouter une catégorie</legend>
        <form action="fields.php?add=on" method="post">
            <div>
                <label for="name">Nom de la catégorie</label>
                <input type="text" name="name" id="name">
            </div>
            <input type="submit" value="Ajouter la catégorie">
        </form>
    </fieldset>

    <div id="information">
        <table>
            <thead>
                <tr>
                    <th>Identifiant</th>
                    <th>Nom</th>
                    <th>Supprimer</th>
                </tr>
            </thead>
            <tbody>
                <?php for ($i = 0; $i < count($fields); $i++) { ?>
                    <tr>
                        <?php for ($index = 0; $index < count($fields[$i]); $index++) { ?>
                            <td><?php echo $fields[$i][$index]; ?></td>
                        <?php } ?>
                        <td class="remove" onclick="removeField(<?php echo intval($fields[$i][0]); ?>)"><img src="../../assets/icons/remove.png" alt="Icone de suppresion"></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <script src="../../JS/icons_for_gestionning.js"></script>
</body>
</html>
