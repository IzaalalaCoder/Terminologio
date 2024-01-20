<!-- Liste tout les concepts -->
<?php
include "../model/Database.php";
include "../model/Member.php";
include "../model/Fields.php";
include "../model/Lang.php";
include "../model/Concept.php";

$pdo = Database::startSession();

if (!isset($_SESSION['IDMEM']) || isset($_SESSION['ISADMIN'])) {
    header("Location: ../concept/concepts.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes favoris</title>
    <link rel="stylesheet" href="../../CSS/general/menu.css">
    <link rel="stylesheet" href="../../CSS/general/all.css">
    <link rel="stylesheet" href="../../CSS/concept/display.css">
</head>
<body>
    <header>
        <?php include "../general/menu.php"; ?>
        <script> clickedItem("profil") </script>
    </header>
    <?php include "sub_menu.php"; ?>
    <div id="concepts_div">
        <!-- Liste les concepts -->
        <?php if (Concept::getNumberMarkedConcepts($pdo, $_SESSION['IDMEM']) == 0) { ?>
            <div class="empty">
                <p class="empty"> Aucun concept n'est ajouté dans les favoris </p>
            </div>
        <?php } else {
            $allConcept = Concept::getAllMarkedConcepts($pdo, $_SESSION['IDMEM']);
            for ($i = 0; $i < count($allConcept); $i++) { ?>
                <div class="concept">
                    <aside class="repr">
                        <img src="<?php echo $allConcept[$i][2]; ?>" alt="Image représentant le concept <?php echo $allConcept[$i][1]; ?>">
                    </aside>
                    <aside class="information">
                        <h3><?php echo $allConcept[$i][1]; ?></h3>
                        <h3>Réalisé par <?php echo Member::getAllFieldsOfMemberWithIdentifiant($pdo, $allConcept[$i][4])['PSEUDOMEM']; ?></h3>
                        <h3>Ecrit en <?php echo Lang::getLang($pdo, $allConcept[$i][3])[1]; ?> </h3>
                        <?php $translates = Concept::getAllTranslateOnConcept($pdo, intval($allConcept[$i][0]));
                            if (count($translates) > 0) { ?>
                            <div class="translate">
                                <h2>Traduction disponibles</h2>
                                <?php foreach ($translates as $t) { ?>
                                    <div class="lang">
                                        <span><?php echo Lang::getLang($pdo, $t)[2]; ?></span>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } else { ?>
                            <div class="not">
                                <p>Pas de traductions disponibles</p>
                            </div>
                        <?php }
                        
                            // echo date("j", time() - fileatime($allConcept[$i][2]));
                            // $now = date_create("now");
                            // $date_file = date_create(date("Y-n-j", fileatime($allConcept[$i][2])));
                            // $diff = date_diff($date_file, $now);
                            // echo $diff->format('%R%a days');
                        ?>
                        
                        <div class="cat">
                            <?php
                                $field = Fields::getField($pdo, Fields::getFieldAssociateConcept($pdo, intval($allConcept[$i][0]))); 
                                if (count($field) > 0) { echo $field['NAMECATEGORIE']; } else { ?>
                                    <div class="not">
                                        <p>Absence de catégorie</p>
                                    </div>
                            <?php } ?>
                        </div>
                    </aside>
                    </aside>
                    <aside class="icons">
                        <button onclick="viewMore(<?php echo intval($allConcept[$i][0]); ?>)"><img src="../../assets/icons/more.png" title="Voir plus" alt="Icone voir plus"></button>
                        <button onclick="editConcept(<?php echo intval($allConcept[$i][0]); ?>)"><img src="../../assets/icons/edit.png" title="Editer" alt="Icone de modification"></button>
                        <button onclick="addTranslate(<?php echo intval($allConcept[$i][0]); ?>)"><img src="../../assets/icons/translate.png" title="Traduire" alt="Icone d'ajout de traduction"></button>
                        <button onclick="removeFavorite(<?php echo $_SESSION['IDMEM']; ?>, <?php echo intval($allConcept[$i][0]); ?>, '../member/favorite.php')"><img src="../../assets/icons/fav.png" title="Dans mes favoris" alt="Icone de favori"></button>
                        <!-- <button><img src="../../assets/icons/edit.png" title="Editer" alt="Icone de modification"></button> -->
                    </aside>
                </div>
            <?php }
        } ?>
    </div>
    <script src="../../JS/icons_for_concept.js"></script>
</body>
</html>
