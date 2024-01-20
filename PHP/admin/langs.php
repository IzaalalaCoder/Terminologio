<?php

include "../model/Database.php";
include "../model/Lang.php";
include "../model/Concept.php";

$pdo = Database::startSession();

if (!isset($_SESSION['ISADMIN'])) {
    header("Location: ../concept/concepts.php");
    exit();
}

$langs = Lang::getAllLanguages($pdo);

if (isset($_GET['add']) && $_GET['add'] == 'on') {
    if (isset($_POST['name']) && !empty($_POST['name'])) {
        if (isset($_POST['code']) && !empty($_POST['code'])) {
            if (isset($_POST['title']) && !empty($_POST['title'])) {
                if (!Lang::isExistLanguageWithName($pdo, $_POST['name'])) {
                    Lang::addLang($pdo, $_POST['name'], $_POST['code'], $_POST['title']);
                    header('Location: langs.php');
                    exit();
                } else {
                    $error = "La langue existe déjà";
                }
            } else {
                $error = "La saisie de la traduction du composant est vide";
            }
        } else {
            $error = "La saisie du code de la langue est vide";
        }
    } else {
        $error = "La saisie du nom de la langue est vide";
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
        <legend>Ajouter une langue</legend>
        <form action="langs.php?add=on" method="post">
            <div>
                <label for="name">Nom de la langue</label>
                <input type="text" id="name" name="name">
            </div>
            <div>
                <label for="code">Code de la langue</label>
                <input type="text" id="code" name="code">
            </div>
            <div>
                <label for="title">Traduction de Composant</label>
                <input type="text" id="title" name="title">
            </div>
            
            <input type="submit" value="Ajouter la langue">
        </form>
    </fieldset>
    
    <div id="information">
        <table>
            <thead>
                <tr>
                    <th>Identifiant</th>
                    <th>Nom</th>
                    <th>Code</th>
                    <th>Traduction : Composant</th>
                    <th>Supprimer</th>
                </tr>
            </thead>
            <tbody>
                <?php for ($i = 0; $i < count($langs); $i++) { ?>
                    <tr>
                        <?php for ($index = 0; $index < count($langs[$i]); $index++) { ?>
                            <td><?php echo $langs[$i][$index]; ?></td>
                        <?php }
                        $use_lang = Concept::langIsUseByConcepts($pdo, $langs[$i][0]); 
                        if (!$use_lang) { ?>
                            <td class="remove" onclick="removeLang(<?php echo intval($langs[$i][0]); ?>)"><img src="../../assets/icons/remove.png" alt="Icone de suppresion"></td>
                        <?php } else { ?>
                            <td></td>
                        <?php } ?>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <script src="../../JS/icons_for_gestionning.js"></script>
</body>
</html>
