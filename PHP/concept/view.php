<?php

include "../model/Database.php";
include "../model/Member.php";
include "../model/Lang.php";
include "../model/Fields.php";
include "../model/Concept.php";
$pdo = Database::startSession();

if ($_GET['id'] && !empty($_GET['id'])) {
    $id = intval($_GET['id']);
    $informationOnConcept = Concept::getConcept($pdo, $id);
    if (count(Concept::getConcept($pdo, $id)) == 0) {
        header('Location: ../concept/concepts.php');
        exit();
    }
    $informationOfComponents = Concept::getAllComponentsOfConcept($pdo, $informationOnConcept['IDCON']);
    // print_r($informationOnConcept);
} else {
    header("Location: ../concept/concepts.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../CSS/concept/add.css">
    <link rel="stylesheet" href="../../CSS/general/menu.css">
    <link rel="stylesheet" href="../../CSS/concept/view.css">
    <title><?php echo $informationOnConcept['TITLECON']; ?></title>
    <?php if (!isset($_SESSION['IDMEM']) || isset($_SESSION['ISADMIN'])) { ?>
        <style>
            .area {
                background-color: #000;
                color: white;
            }
        </style>
    <?php } else {
        $parameters = Member::getAllParameterOfMember($pdo, $_SESSION['IDMEM']); ?>
        <style>
            .area {
                background-color: <?php echo $parameters['COLORPARAM']; ?> ;
                color: white;
            }
        </style>
    <?php } ?>
</head>
<body>
    <header>
        <?php include "../general/menu.php"; ?>
        <script> clickedItem("concepts") </script>
    </header>
    <div id="title">
        <h3>
            <?php
            echo $informationOnConcept['TITLECON'];
            $field = Fields::getField($pdo, Fields::getFieldAssociateConcept($pdo, $id));
            echo (count($field) > 0) ? ' | ' . $field['NAMECATEGORIE'] : "";
            if (isset($_POST['lang'])) { ?>
                | Traduction en <?php echo Lang::getLang($pdo, intval($_POST['lang']))[1]; }
            ?>
        </h3>
    </div>
    <div id="concept_div">
        <div id="image" >
            <!-- <div id="image"> -->
                <!-- <img onmousemove="moveOnImage(event)" id="imported" src="<php echo $_GET['url']; ?>" alt="Image importée"> -->
            <img id="imported" src="<?php echo $informationOnConcept['REPRESENTATIONCON'] ?>" alt="Image importée">
        </div>
        <div id="areas">
            <?php $components = Concept::getAllComponentsOfConcept($pdo, $informationOnConcept['IDCON']);
            // print_r($components);
            for ($i = 0; $i < count($components); $i++) { ?>
                <div style="<?php echo 'top: ' . $components[$i][2] . 'px; left: '. $components[$i][1] . 'px;'; ?>"
                    class="area C<?php echo ($i + 1);?>">
                    <?php echo ($i + 1);?>
                </div>
            <?php }
            ?>
        </div>
    </div>

    <?php if (!isset($_POST['translate']) || !isset($_POST['lang'])) { 
        if (isset($_POST['translate'])) { ?>
            <script>
                alert("Veuillez choisir une traduction");
            </script>
        <?php } ?>
        <div id="components">
            <?php for ($i = 0; $i < count($informationOfComponents); $i++) { ?>
                <div class="comp C<?php echo ($i + 1);?>">
                    <img src="../../assets/icons/component.png" alt="Icone composant">
                    <h3><?php echo ($i + 1); ?> | <?php echo $informationOfComponents[$i][3]; ?></h3>
                    <?php if (isset ($_SESSION['ISADMIN'])) { ?>
                        <div class="remove" onclick="removeComponent(<?php echo intval($informationOfComponents[$i][0]); ?>, <?php echo intval($informationOnConcept['IDCON']); ?>)">
                            <img src="../../assets/icons/remove.png" alt="Icone de suppresion">
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
    <?php } else { ?>
        <?php if (isset($_POST['translate'])) {
            if (isset($_POST['lang'])) {
                $informationOnTranslate = Concept::getTranslateOnConceptInLang($pdo, intval($_GET['id']), intval($_POST['lang'])); ?>
                <div id="translate">
                    <?php for ($i = 0; $i < count($informationOnTranslate); $i++) { ?>
                        <div class="comp C<?php echo ($i + 1);?>">
                            <img src="../../assets/icons/component.png" alt="Icone composant">
                            <h3><?php echo ($i + 1); ?> | <?php echo $informationOnTranslate[$i][1]; ?></h3>
                        </div>
                    <?php } ?>
                </div>
                <?php if (isset($_SESSION['ISADMIN'])) { ?>
                    <div class="remove">
                        <h2>Supprimer la traduction</h2>
                        <button onclick="removeTranslate(<?php echo intval($informationOnConcept['IDCON']); ?>, <?php echo $_POST['lang']; ?>)"><img src="../../assets/icons/remove.png" title="Supprimer" alt="Icone de suppresion"></button>
                    </div>
                <?php } ?>
    <?php } } } ?>

    <?php $translates = Concept::getAllTranslateOnConcept($pdo, intval($_GET['id'])); 
        if (count($translates) > 0) { ?>
            <h2>Ce concept a également été écrit en </h2>
            <form action="view.php?id=<?php echo $id; ?>" method="post">
                <label for="lang">Choisir une langue :</label>
                <select name="lang" id="lang" size="5">
                    <?php for ($i = 0; $i < count($translates); $i++) {
                        $infoOnLanguage = Lang::getLang($pdo, intval($translates[$i])); ?>
                        <option value="<?php echo $infoOnLanguage[0]; ?>">
                            <?php echo $infoOnLanguage[1]; ?>
                        </option>
                    <?php } ?>
                </select>
                <input type="submit" value="Valider" name="translate">
            </form>
            <?php if (isset($_POST['translate']) && isset($_POST['lang'])) { ?>
                <button onclick="resetConcept(<?php echo $id; ?>)">Revenir</button>
            <?php } }?>
    <?php if (isset($_SESSION['ISADMIN'])) { ?>
        <div id="admin_remove">
            <div class="remove_div">
                <h2>Supprimer le concept </h2>
                <button class="remove" onclick="removeConcept(<?php echo intval($informationOnConcept['IDCON']); ?>)"><img src="../../assets/icons/remove.png" title="Supprimer" alt="Icone de suppresion"></button>
            </div>
            <?php if (count($field) > 0) { ?>
                <div class="remove_div">
                    <h2>Supprimer la catégorie</h2>
                    <button class="remove" onclick="dissociateConceptOfField(<?php echo intval($informationOnConcept['IDCON']); ?>, <?php echo intval($field['IDCATEGORIE']); ?>)"><img src="../../assets/icons/remove.png" title="Supprimer" alt="Icone de suppresion"></button>
                </div>
            <?php } ?>
        </div>
    <?php } ?>
    <script src="../../JS/icons_for_concept.js"></script>
    <script src="../../JS/icons_for_gestionning.js"></script>
    <script src="../../JS/modify_concept.js"></script>
    <script src="../../JS/view.js"></script>
</body>
</html>
