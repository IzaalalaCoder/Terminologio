<?php
include "../model/Database.php";
include "../model/Lang.php";
include "../model/Fields.php";
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
    <title><?php echo $_SESSION['PSEUDOMEM']; ?></title>
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
        <?php if (count(Concept::getAllConceptsRealizedByMember($pdo, $_SESSION['IDMEM'])) == 0) { ?>
            <div class="empty">
                <p class="empty"> Aucun concept n'a été réalisé par <?php echo $_SESSION['PSEUDOMEM']; ?> </p>
            </div>
        <?php } else {
            $allConcept = Concept::getAllConceptsRealizedByMember($pdo, $_SESSION['IDMEM']);
            for ($i = 0; $i < count($allConcept); $i++) { ?>
                <div class="concept">
                    <aside class="repr">
                        <img src="<?php echo $allConcept[$i][2]; ?>" alt="Image représentant le concept <?php echo $allConcept[$i][1]; ?>">
                    </aside>
                    <aside class="information">
                        <h3><?php echo $allConcept[$i][1]; ?></h3>
                        <h3>Réalisé par <?php echo $_SESSION['PSEUDOMEM']; ?></h3>
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
                        <?php if (Concept::isMarkedConceptByMember($pdo, $_SESSION['IDMEM'], intval($allConcept[$i][0]))) { ?>
                            <button onclick="removeFavorite(<?php echo $_SESSION['IDMEM']; ?>, <?php echo intval($allConcept[$i][0]); ?>, '../member/profil.php')" ><img src="../../assets/icons/fav.png" title="Dans mes favoris" alt="Icone de favori"></button>
                        <?php } else { ?>
                            <button onclick="addFavorite(<?php echo $_SESSION['IDMEM']; ?>, <?php echo intval($allConcept[$i][0]); ?>, '../member/profil.php')"><img src="../../assets/icons/not_fav.png" title="Pas dans mes favoris" alt="Icone de l'absence de favori"></button>
                        <?php } ?>
                    </aside>
                </div>
            <?php }
        } ?>
    </div>
    <script src="../../JS/icons_for_concept.js"></script>
</body>
</html>
