<?php

include "../model/Database.php";
include "../model/Concept.php";
include "../model/Fields.php";
include "../model/Member.php";
$pdo = Database::startSession();

if (!isset($_SESSION['IDMEM']) || isset($_SESSION['ISADMIN'])) {
    header("Location: ../concept/concepts.php");
    exit();
}

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = intval($_GET['id']);
    $informationOnConcept = Concept::getConcept($pdo, $id);
    $informationOfComponents = Concept::getAllComponentsOfConcept($pdo, $informationOnConcept['IDCON']);
} else {
    header("Location: ../concept/concepts.php");
    exit();
}

if (isset($_GET['update']) && !empty($_GET['update'])) {
    Concept::updateAllComponents($pdo, $_GET['update']);
    header("Location: modify_concept.php?id=" . $_GET['id']);
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
    <title>Modifier <?php echo $informationOnConcept['TITLECON']; ?></title>
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
        <h2>Mise à jour du concept : <?php echo $informationOnConcept['TITLECON']; ?></h2>
    </div>
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
            <div class="comp" id="<?php echo "C" . $informationOfComponents[$i][0]?>">
                <img src="../../assets/icons/component.png" alt="Icone composant">
                <h3><?php echo ($i + 1); ?> | <span contenteditable="true"><?php echo $informationOfComponents[$i][3]; ?></span></h3>
            </div>
        <?php } ?>
    </div>

    <?php if (!Fields::isAssociatedWithField($pdo, $id)) { ?>
        <div id="choice_cat">
            <form action="add.php?state=field&concept=<?php echo intval($_GET['id']); ?>&dest=modify_concept.php" method="post">
                <label for="field">Choisir une catégorie :</label>
                <select name="field" id="field" size="5">
                    <?php
                        $fields = Fields::getAllFields($pdo);
                        for ($i = 0; $i < count($fields); $i++) { ?>
                            <option value="<?php echo $fields[$i][0]; ?>">
                                <?php echo $fields[$i][1]; ?>
                            </option>
                        <?php }
                    ?>
                </select>
                <input type="submit" value="Ajouter la catégorie">
            </form>
        </div>
    <?php } ?>

    <div id="validate">
        <button onclick="changeTerminology(<?php echo $_GET['id']; ?>)">Réaliser la mise à jour</button>
    </div>

    <script src="../../JS/icons_for_concept.js"></script>
    <script src="../../JS/modify_concept.js"></script>
</body>
</html>

