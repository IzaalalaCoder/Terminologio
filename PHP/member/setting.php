<!-- Liste tout les concepts -->
<?php
include "../model/Database.php";
include "../model/Member.php";
include "../model/Lang.php";

$pdo = Database::startSession();

if (!isset($_SESSION['IDMEM']) || isset($_SESSION['ISADMIN'])) {
    header("Location: ../concept/concepts.php");
    exit();
} else if (isset($_POST['update'])) {
    // Information de profil ***********************************************************************************************
    $changeOnMember = array();

    // Le prénom
    if (isset($_POST['FIRSTNAMEMEM']) && !empty($_POST['FIRSTNAMEMEM'])) {
        if (preg_match("#^[A-Z][a-z]{2,}(-[A-Z][a-z]{2,})?$#", $_POST['FIRSTNAMEMEM']) != 0) {
            if (strcmp($_POST['FIRSTNAMEMEM'], $_SESSION['FIRSTNAMEMEM']) != 0) {
                array_push($changeOnMember, array('FIRSTNAMEMEM', $_POST['FIRSTNAMEMEM']));
                $_SESSION['FIRSTNAMEMEM'] = $_POST['FIRSTNAMEMEM'];
            }
        }
    }

    // Le nom de famille
    if (isset($_POST['LASTNAMEMEM']) && !empty($_POST['LASTNAMEMEM'])) {
        if (preg_match("#^[A-Z][a-z]{2,}(-[A-Z][a-z]{2,})?$#", $_POST['LASTNAMEMEM']) != 0) {
            if (strcmp($_POST['LASTNAMEMEM'], $_SESSION['LASTNAMEMEM']) != 0) {
                array_push($changeOnMember, array('LASTNAMEMEM', $_POST['LASTNAMEMEM']));
                $_SESSION['LASTNAMEMEM'] = $_POST['LASTNAMEMEM'];
            }
        }
    }

    // L'adresse mail
    if (isset($_POST['MAILMEM']) && !empty($_POST['MAILMEM'])) {
        if (preg_match('/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))/i', $_POST['MAILMEM']) != 0) {
            if (strcmp($_POST['MAILMEM'], $_SESSION['MAILMEM']) != 0) {
                array_push($changeOnMember, array('MAILMEM', $_POST['MAILMEM']));
                $_SESSION['MAILMEM'] = $_POST['MAILMEM'];
            }
        }
    }

    // Le pseudo
    if (isset($_POST['PSEUDOMEM']) && !empty($_POST['PSEUDOMEM'])) {
        if (preg_match("#^@[-_\.A-Za-z0-9]{3,}$#", $_POST['PSEUDOMEM']) != 0) {
            if (strcmp($_POST['PSEUDOMEM'], $_SESSION['PSEUDOMEM']) != 0) {
                array_push($changeOnMember, array('PSEUDOMEM', $_POST['PSEUDOMEM']));
                $_SESSION['PSEUDOMEM'] = $_POST['PSEUDOMEM'];
            }
        }
    }

    // Mise à jour des données seulement si les données ont été modifiés
    if (count($changeOnMember) > 0) {
        Member::updateMember($pdo, $_SESSION['IDMEM'], $changeOnMember);
        $changeOnMember = array();
    }

    // Mot de passe ***********************************************************************************************
    if (isset($_POST['MDPMEM']) && !empty($_POST['MDPMEM'])) {
        if (isset($_POST['MDPMEM_CONFIRM']) && !empty($_POST['MDPMEM_CONFIRM'])) {
            if (preg_match("#^[-@_\.A-Za-z0-9]{8,}$#", $_POST['MDPMEM']) != 0) {
                if (preg_match("#^[-@_\.A-Za-z0-9]{8,}$#", $_POST['MDPMEM_CONFIRM']) != 0) {
                    if (strcmp($_POST['MDPMEM'], $_POST['MDPMEM_CONFIRM']) == 0) {
                        $pass = hash('sha256', $_POST['MDPMEM']);
                        Member::updateMember($pdo, $_SESSION['IDMEM'], array(array('MDPMEM', $pass)));
                        // echo "mot de passe modifié";
                    }
                }
            }
        }
    }

    // Informations supplémentaires *******************************************************************************

    $allParameters = Member::getAllParameterOfMember($pdo, $_SESSION['IDMEM']);

    // Le nombre de concepts affichées par page
    // if (isset($_POST['NBCONCEPTDISPLAYPARAM']) && !empty($_POST['NBCONCEPTDISPLAYPARAM'])) {
    //     if (preg_match("#^[0-9]*$#", $_POST['NBCONCEPTDISPLAYPARAM']) != 0) {
    //         $number = intval($_POST['NBCONCEPTDISPLAYPARAM']);
    //         if ($number !== $allParameters['NBCONCEPTDISPLAYPARAM']) {
    //             array_push($changeOnMember, array('NBCONCEPTDISPLAYPARAM', $number));
    //         }
    //     }
    // }

    // Le mode d'affichage
    // if (isset($_POST['ISDARKMODEPARAM'])) {
    //     if (!$allParameters['ISDARKMODEPARAM']) {
    //         array_push($changeOnMember, array('ISDARKMODEPARAM', '1'));
    //     }
    // } else {
    //     if ($allParameters['ISDARKMODEPARAM']) {
    //         array_push($changeOnMember, array('ISDARKMODEPARAM', '0'));
    //     }
    // }

    // La couleur associé au zone mise en avant
    if (isset($_POST['color']) && !empty($_POST['color'])) {
        if (strcmp($_POST['color'], $allParameters['COLORPARAM']) != 0) {
            array_push($changeOnMember, array('COLORPARAM', $_POST['color']));
        }
    }

    if (count($changeOnMember) > 0) {
        Member::updateParameterOfMember($pdo, $_SESSION['IDMEM'], $changeOnMember);
    }

    // Les langues utilisées par le membre
    if (isset($_POST['langs']) && !empty($_POST['langs'])) {
        $notExist = false;
        for ($i = 0; $i < count($_POST['langs']); $i++) {
            if (!Lang::isExistLanguage($pdo, $_POST['langs'][$i])) {
                $notExist = true;
                break;
            }
        }
        if ($notExist) {
            $error = "Une erreur est survenue lors du choix des langues";
        }
        Lang::updateLanguagesOfMember($pdo, $_SESSION['IDMEM'], $_POST['langs']);
    }

    header("Location: setting.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes paramètres</title>
    <link rel="stylesheet" href="../../CSS/general/menu.css">
    <link rel="stylesheet" href="../../CSS/general/all.css">
    <link rel="stylesheet" href="../../CSS/member/setting.css">
</head>
<body>
    
    <header>
        <?php include "../general/menu.php"; ?>
        <script> clickedItem("profil") </script>
    </header>
    <?php include "sub_menu.php"; ?>
    <div id="set_member">
        <form action="setting.php" method="post">
            <fieldset>
                <legend><img src="../../assets/icons/info.png" alt=""> - Informations du profil </legend>

                <!-- Le prénom -->
                <div class="bloc_input">
                    <label for="FIRSTNAMEMEM">Prénom</label>
                    <input type="text" name="FIRSTNAMEMEM" id="FIRSTNAMEMEM" value="<?php echo $_SESSION['FIRSTNAMEMEM']; ?>">
                </div>

                <!-- Le nom de famille -->
                <div class="bloc_input">
                    <label for="LASTNAMEMEM">Nom de famille</label>
                    <input type="text" name="LASTNAMEMEM" id="LASTNAMEMEM" value="<?php echo $_SESSION['LASTNAMEMEM']; ?>">
                </div>

                <!-- L'adresse mail -->
                <div class="bloc_input">
                    <label for="MAILMEM">Adresse mail</label>
                    <input type="text" name="MAILMEM" id="MAILMEM" value="<?php echo $_SESSION['MAILMEM']; ?>">
                </div>

                <!-- Le pseudo -->
                <div class="bloc_input">
                    <label for="PSEUDOMEM">Pseudo</label>
                    <input type="text" name="PSEUDOMEM" id="PSEUDOMEM" value="<?php echo $_SESSION['PSEUDOMEM'] ?>">
                </div>
            </fieldset>

            <fieldset>
                <legend><img src="../../assets/icons/private.png" alt=""> - Mot de passe </legend>

                <!-- Le mot de passe -->
                <div class="bloc_input">
                    <label for="MDPMEM">Nouveau mot de passe</label>
                    <input type="password" name="MDPMEM" id="MDPMEM" value="">
                </div>

                <!-- Confirmation du mot de passe  -->
                <div class="bloc_input">
                    <label for="MDPMEM_CONFIRM">Confirmation du mot de passe</label>
                    <input type="password" name="MDPMEM_CONFIRM" id="MDPMEM_CONFIRM" value="">
                </div>
            </fieldset>

            <fieldset>
                <legend><img src="../../assets/icons/more.png" alt=""> - Informations supplémentaires </legend>

                <?php $parameter = Member::getAllParameterOfMember($pdo, $_SESSION['IDMEM']); ?>
                <!-- Le mode d'affichage -->
                <!-- <div class="bloc_input">
                    <label for="ISDARKMODEPARAM">Dark mode</label>
                    <input type="checkbox" name="ISDARKMODEPARAM" id="ISDARKMODEPARAM" <hp if ($parameter['ISDARKMODEPARAM']) { ?> checked php } ?>>
                </div> -->

                <!-- Le mode d'affichage -->
                <div class="bloc_input">
                    <label for="langs">Choisir les langues :</label>
                    <select name="langs[]" id="langs" size="5" multiple>
                        <?php
                            foreach (Lang::getAllLanguages($pdo) as $key => $value) { ?>
                                <option value="<?php echo $value[0]; ?>" <?php if (Lang::isUseByMember($pdo, $_SESSION['IDMEM'], $value[0])) { ?> selected <?php } ?>>
                                    <?php echo $value[1]; ?>
                                </option>
                            <?php }
                        ?>
                    </select>
                </div>

                <!-- La couleur des zones -->
                <div class="bloc_input">
                    <label for="color">Choisir une couleur</label>
                    <input type="color" name="color" id="color" value="<?php echo $parameter['COLORPARAM']; ?>">
                </div>

                <!-- Le nombre de concept d'affichés
                <div class="bloc_input">
                    <label for="NBCONCEPTDISPLAYPARAM">Nombre des derniers éléments affichées</label>
                    <input type="text" name="NBCONCEPTDISPLAYPARAM" id="NBCONCEPTDISPLAYPARAM" value="< echo $parameter['NBCONCEPTDISPLAYPARAM']; ?>">
                </div> -->
                
            </fieldset>

            

            <input type="submit" name="update" value="Modifier mes informations">
        </form>
    </div>
</body>
</html>
