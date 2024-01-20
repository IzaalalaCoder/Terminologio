<?php
include "../model/Database.php";
include "../model/Fields.php";
include "../model/Concept.php";
include "../model/Lang.php";
include "../model/Member.php";

// if (isset($_GET['ask']) && $_GET['ask'] == 'on') {
//     if (!isset($_POST['searchOn']) || !(isset($_POST['cat']))) {
//         header('Location: search.php');
//         exit();
//     }
// }

$pdo = Database::startSession();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recherche</title>
    <link rel="stylesheet" href="../../CSS/general/menu.css">
    <link rel="stylesheet" href="../../CSS/general/all.css">
    <link rel="stylesheet" href="../../CSS/concept/display.css">
</head>
<body>
    <header>
        <?php include "../general/menu.php"; ?>
        <script> clickedItem("search") </script>
    </header>
    
    <div id="form_search">
        <form action="search.php?ask=on" method="post">
            <label for="searchOn">Rechercher : </label>
            <input type="search" name="searchOn" id="searchOn">
        </form>
        <div id="fields_clickable">
            <?php $fields = Fields::getAllFields($pdo); ?>
            <select id="selectField">
                <option selected disabled>Choisir une catégorie</option>
                <?php for ($i = 0; $i < count($fields); $i++) { ?>
                    <option value="<?php echo $fields[$i][0]; ?>">
                        <?php echo $fields[$i][1]; ?>
                    </option>
                <?php } ?>
            </select>
        </div>
    </div>
    <?php if (isset($_GET['ask']) && $_GET['ask'] == 'on') { ?>
        <div id="concepts_div">
            <?php if (isset($_GET['searchOn']) && $_GET['searchOn'] == 'cat') {
                if (isset($_GET['cat']) && !empty($_GET['cat'])) {
                    $field = Fields::getField($pdo, intval($_GET['cat']));
                    $concepts = Fields::searchInFields($pdo, intval($_GET['cat']));
                    if (count($concepts) == 0) { ?>
                        <div id="not">
                            Aucun concept n'est associé à la catégorie <?php echo $field['NAMECATEGORIE']; ?>
                        </div>
                    <?php } else { ?>
                    <h2>Recherche de concept avec : <?php echo $field['NAMECATEGORIE']; ?></h2>
                    <?php foreach ($concepts as $c) { 
                        $concept = Concept::getConcept($pdo, $c); ?>
                        <div class="concept">
                            <aside class="repr">
                                <img src="<?php echo $concept['REPRESENTATIONCON']; ?>" alt="Image représentant le concept <?php echo $concept['TITLECON']; ?>">
                            </aside>
                            <aside class="information">
                                <h3><?php echo $concept['TITLECON']; ?></h3>
                                <h3>Réalisé par <?php echo Member::getAllFieldsOfMemberWithIdentifiant($pdo, $concept['AUTHORCON'])['PSEUDOMEM']; ?></h3>
                                <h3>Ecrit en <?php echo Lang::getLang($pdo, intval($concept['DEFAULTLANGIDCON']))[1]; ?> </h3>
                                <?php $translates = Concept::getAllTranslateOnConcept($pdo, intval($concept['IDCON'])); 
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
                                        $field = Fields::getField($pdo, Fields::getFieldAssociateConcept($pdo, intval($concept['IDCON']))); 
                                        if (count($field) > 0) { echo $field['NAMECATEGORIE']; } else { ?>
                                            <div class="not">
                                                <p>Absence de catégorie</p>
                                            </div>
                                    <?php } ?>
                                </div>
                            </aside>
                            <aside class="icons">
                                <button onclick="viewMore(<?php echo intval($concept['IDCON']); ?>)"><img src="../../assets/icons/more.png" title="Voir plus" alt="Icone voir plus"></button>
                                <?php if (isset($_SESSION['IDMEM'])) {
                                    if (!isset($_SESSION['ISADMIN'])) { ?>
                                        <button onclick="editConcept(<?php echo intval($concept['IDCON']); ?>)"><img src="../../assets/icons/edit.png" title="Voir plus" alt="Icone de modification"></button>
                                        <button onclick="addTranslate(<?php echo intval($concept['IDCON']); ?>)"><img src="../../assets/icons/translate.png" title="Traduire" alt="Icone d'ajout de traduction"></button>
                                        <?php if (Concept::isMarkedConceptByMember($pdo, $_SESSION['IDMEM'], intval($concept['IDCON']))) { ?>
                                            <button onclick="removeFavorite(<?php echo $_SESSION['IDMEM']; ?>, <?php echo intval($concept['IDCON']); ?>, '../concept/concepts.php')" ><img src="../../assets/icons/fav.png" title="Dans mes favoris" alt="Icone de favori"></button>
                                        <?php } else { ?>
                                            <button onclick="addFavorite(<?php echo $_SESSION['IDMEM']; ?>, <?php echo intval($concept['IDCON']); ?>, '../concept/concepts.php')"><img src="../../assets/icons/not_fav.png" title="Pas dans mes favoris" alt="Icone de l'absence de favori"></button>
                                        <?php }
                                    } else { ?>
                                        <button onclick="removeConcept(<?php echo intval($concept['IDCON']); ?>)"><img src="../../assets/icons/remove.png" title="Supprimer" alt="Icone de suppresion"></button>
                                    <?php }
                                } ?>
                                    
                                <!-- <button><img src="../../assets/icons/edit.png" title="Editer" alt="Icone de modification"></button> -->
                            </aside>
                        </div>
            <?php } } } } else {
                $concepts = Concept::searchConcept($pdo, $_POST['searchOn']);
                if (count($concepts) == 0) { ?>
                    <div id="not">
                        Aucun concept n'est associé à la recherche <?php echo $_POST['searchOn']; ?>
                    </div>
                <?php } else { ?>
                    <h2>Recherche de concept avec : <?php echo $_POST['searchOn']; ?> </h2>
                <?php foreach ($concepts as $c) {
                    // print_r($c);
                    $concept = Concept::getConcept($pdo, $c['IDCON']);?>
                <div class="concept">
                    <aside class="repr">
                        <img src="<?php echo $concept['REPRESENTATIONCON']; ?>" alt="Image représentant le concept <?php echo $concept['TITLECON']; ?>">
                    </aside>
                    <aside class="information">
                        <h3><?php echo $concept['TITLECON']; ?></h3>
                        <h3>Réalisé par <?php echo Member::getAllFieldsOfMemberWithIdentifiant($pdo, $concept['AUTHORCON'])['PSEUDOMEM']; ?></h3>
                        <h3>Ecrit en <?php echo Lang::getLang($pdo, intval($concept['DEFAULTLANGIDCON']))[1]; ?> </h3>
                        <?php $translates = Concept::getAllTranslateOnConcept($pdo, intval($concept['IDCON']));
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
                                $field = Fields::getField($pdo, Fields::getFieldAssociateConcept($pdo, intval($concept['IDCON'])));
                                if (count($field) > 0) { echo $field['NAMECATEGORIE']; } else { ?>
                                    <div class="not">
                                        <p>Absence de catégorie</p>
                                    </div>
                            <?php } ?>
                        </div>
                    </aside>
                    <aside class="icons">
                        <button onclick="viewMore(<?php echo intval($concept['IDCON']); ?>)"><img src="../../assets/icons/more.png" title="Voir plus" alt="Icone voir plus"></button>
                        <?php if (isset($_SESSION['IDMEM'])) {
                            if (!isset($_SESSION['ISADMIN'])) { ?>
                                <button onclick="editConcept(<?php echo intval($concept['IDCON']); ?>)"><img src="../../assets/icons/edit.png" title="Voir plus" alt="Icone de modification"></button>
                                <button onclick="addTranslate(<?php echo intval($concept['IDCON']); ?>)"><img src="../../assets/icons/translate.png" title="Traduire" alt="Icone d'ajout de traduction"></button>
                                <?php if (Concept::isMarkedConceptByMember($pdo, $_SESSION['IDMEM'], intval($concept['IDCON']))) { ?>
                                    <button onclick="removeFavorite(<?php echo $_SESSION['IDMEM']; ?>, <?php echo intval($concept['IDCON']); ?>, '../concept/concepts.php')" ><img src="../../assets/icons/fav.png" title="Dans mes favoris" alt="Icone de favori"></button>
                                <?php } else { ?>
                                    <button onclick="addFavorite(<?php echo $_SESSION['IDMEM']; ?>, <?php echo intval($concept['IDCON']); ?>, '../concept/concepts.php')"><img src="../../assets/icons/not_fav.png" title="Pas dans mes favoris" alt="Icone de l'absence de favori"></button>
                                <?php }
                            } else { ?>
                                <button onclick="removeConcept(<?php echo intval($concept['IDCON']); ?>)"><img src="../../assets/icons/remove.png" title="Supprimer" alt="Icone de suppresion"></button>
                            <?php }
                        } ?>
                            
                        <!-- <button><img src="../../assets/icons/edit.png" title="Editer" alt="Icone de modification"></button> -->
                    </aside>
                </div>
            <?php } } } ?>
        </div>
    <?php  } ?>

    <script src="../../JS/search.js"></script>
    <script src="../../JS/icons_for_concept.js"></script>
    <script src="../../JS/icons_for_gestionning.js"></script>
    <script>
        var field = document.getElementById("selectField");
        field.addEventListener("change", function() {
            searchWithField(field.value);
        });
    </script>
</body>
</html>
