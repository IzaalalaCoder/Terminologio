<?php

include "../model/Database.php";
include "../model/Member.php";
include "../model/Concept.php";
include "../model/Lang.php";
$pdo = Database::startSession();

if (!isset($_SESSION['IDMEM']) || isset($_SESSION['ISADMIN'])) {
    header("Location: ../concept/concepts.php");
    exit();
}

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = intval($_GET['id']);
    $informationOnConcept = Concept::getConcept($pdo, $id);
    if (count($informationOnConcept) == 0) {
        header('Location: concepts.php');
        exit();
    }
    $informationOfComponents = Concept::getAllComponentsOfConcept($pdo, $informationOnConcept['IDCON']);
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
    <link rel="stylesheet" href="../../CSS/general/all.css">
    <link rel="stylesheet" href="../../CSS/concept/view.css">
    <title>Traduire <?php echo $informationOnConcept['TITLECON']; ?></title>
    <?php $parameters = Member::getAllParameterOfMember($pdo, $_SESSION['IDMEM']); ?>
    <style>
        .area {
            background-color: <?php echo $parameters['COLORPARAM']; ?> ;
            color: white;
        }
    </style>
</head>
<body>
    <header>
        <?php include "../general/menu.php"; ?>
        <script> clickedItem("concepts") </script>
    </header>
    <div id="title">
        <h3><?php echo (isset($_POST['lang']) ? "Traduction de " . $informationOnConcept['TITLECON'] . " en " . Lang::getLang($pdo, intval($_POST['lang']))[1] : "Choix d'une langue"); ?></h3>
    </div>
    <?php $default = intval($informationOnConcept['DEFAULTLANGIDCON']);
    if (!Lang::canTranslateWithoutUseDefaultLang($pdo, intval($_SESSION['IDMEM']), $default)) { ?>
        <div class="not">
            Pour réaliser les traductions sur <?php echo $informationOnConcept['TITLECON']; ?>, veuillez modifier vos langues de prédilection dans les paramètres.
        </div>
    <?php } else {
        if (!isset($_POST['lang'])) {
            $uses = Lang::getAllLanguagesUseByMember($pdo, $_SESSION['IDMEM']);
            if (count($uses) > 0) { ?>
                <div id="choice_lang">
                    <form action="add_translate.php?id=<?php echo $_GET['id']; ?>" method="post">
                        <label for="lang">Choisir une langue de traduction :</label>
                        <select name="lang" id="lang" size="5" required>
                            <?php
                                
                                for ($i = 0; $i < count($uses); $i++) {
                                    $infoOnLang = Lang::getLang($pdo, intval($uses[$i])); 
                                    if (Concept::getDefaultLang($pdo, intval($_GET['id'])) != intval($infoOnLang[0])) { ?>
                                        <option value="<?php echo $infoOnLang[0]; ?>">
                                            <?php echo $infoOnLang[1]; ?>
                                        </option>
                                <?php } }
                            ?>
                        </select>
                        <input type="submit" value="Valider" name="validate">
                    </form>
                </div>
            <?php }
        } else { ?>
            <div id="concept_div">
                <div id="image" >
                    <img id="imported" src="<?php echo $informationOnConcept['REPRESENTATIONCON'] ?>" alt="Image importée">
                </div>
                <div id="areas">
                    <?php for ($i = 0; $i < count($informationOfComponents); $i++) { ?>
                        <div style="<?php echo 'top: ' . $informationOfComponents[$i][2] . 'px; left: '. $informationOfComponents[$i][1] . 'px;'; ?>"
                            class="area">
                            <?php echo ($i + 1);?>
                        </div>
                    <?php }
                    ?>
                </div>
            </div>
        
            <div id="components">
                <?php for ($i = 0; $i < count($informationOfComponents); $i++) { ?>
                    <div class="comp">
                        <img src="../../assets/icons/component.png" alt="Icone composant">
                        <h3><?php echo ($i + 1); ?> | <span class="edit" id="<?php echo "C" . $informationOfComponents[$i][0]?>" contenteditable="true"><?php echo $informationOfComponents[$i][3]; ?></span></h3>
                    </div>
                    <?php } ?>
            </div>
            <div id="validate">
                <button onclick="addTranslateInBDD(<?php echo $_GET['id']; ?>, <?php echo $_POST['lang']; ?>)">Ajouter une traduction</button>
            </div>
        <?php }
    } ?>
    <script src="../../JS/icons_for_concept.js"></script>
    <script src="../../JS/modify_concept.js"></script>
</body>
</html>
