<?php

/**
 * La page register.php permet la création du compte d'un nouveau membre
 * de la plateforme.
 */
include "../model/Database.php";
include "../model/Member.php";
include "../model/Lang.php";
$pdo = Database::startSession();

// if (!isset($_SESSION['IDMEM'])) {
//     header("Location: ../concept/concepts.php");
//     exit();
// }

if (isset($_POST['adding'])) {
    if (isset($_POST['FIRSTNAMEMEM']) && !empty($_POST['FIRSTNAMEMEM'])) {
        if (preg_match("#^[A-Z][a-z]{2,}(-[A-Z][a-z]{2,})?$#", $_POST['FIRSTNAMEMEM']) == 0) {
            $error = "Le nom ne respecte pas le format";
        } else if (isset($_POST['LASTNAMEMEM']) && !empty($_POST['LASTNAMEMEM'])) {
            if (preg_match("#^[A-Z][a-z]{2,}(-[A-Z][a-z]{2,})?$#", $_POST['LASTNAMEMEM']) == 0) {
                $error = "Le nom de famille ne respecte pas le format demandées";
            } else if (isset($_POST['langs']) && !empty($_POST['langs'])) {
                $notExist = false;
                for ($i = 0; $i < count($_POST['langs']); $i++) {
                    if (!Lang::isExistLanguage($pdo, $_POST['langs'][$i])) {
                        $notExist = true;
                        break;
                    }
                }
                if ($notExist) {
                    $error = "Une erreur est survenue lors du choix des langues";
                } else if (isset($_POST['MAILMEM']) && !empty($_POST['MAILMEM'])) {
                    if (preg_match('/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))/i', $_POST['MAILMEM']) == 0) {
                        $error = "L'email saisi ne correspond pas au format demandé.";
                    } else if (isset($_POST['PSEUDOMEM']) && !empty($_POST['PSEUDOMEM'])) {
                        if (preg_match("#^@[-_\.A-Za-z0-9]{3,}$#", $_POST['PSEUDOMEM']) == 0) {
                            $error = "Le pseudo ne respecte pas le format demandé";
                        } else {
                            // Tests des saisies réussites -> test de la base de données
                            // Test du pseudo
                            if (Member::isExistPseudo($pdo, $_POST['PSEUDOMEM'])) {
                                $error = "Le pseudo existe déjà";
                            } else {
                                if (Member::isExistEmail($pdo, $_POST['MAILMEM'])) {
                                    $error = "L'adresse mail est déjà utilisé";
                                } else {
                                    $pass = "";
                                    if (!isset($_SESSION['ISADMIN'])) {
                                        if (isset($_POST['MDPMEM']) && !empty($_POST['MDPMEM'])) {
                                            if (isset($_POST['MDPMEM_CONFIRM']) && !empty($_POST['MDPMEM_CONFIRM'])) {
                                                if (preg_match("#^[-@_\.A-Za-z0-9]{8,}$#", $_POST['MDPMEM']) != 0) {
                                                    if (preg_match("#^[-@_\.A-Za-z0-9]{8,}$#", $_POST['MDPMEM_CONFIRM']) != 0) {
                                                        if (strcmp($_POST['MDPMEM'], $_POST['MDPMEM_CONFIRM']) == 0) {
                                                            $pass = hash('sha256', $_POST['MDPMEM']);
                                                        } else {
                                                            $error = "Le mot de passe et la confirmation du mot de pass est différente";
                                                        }
                                                    } else {
                                                        $error = "La confirmation du mot passe ne correspond pas au format demandé";
                                                    }
                                                } else {
                                                    $error = "Le mot de passe ne correspond pas au format demandé";
                                                }
                                            } else {
                                                $error = "La saisie de la reconfirmation du mot de passe est vide";
                                            }
                                        } else {
                                            $error = "Le mot de passe est vide";
                                        }
                                    } else {
                                        $not_hash = Member::getAleaPassword();
                                        $pass = hash('sha256', $not_hash);
                                    }

                                    $informationOfMember = array(
                                        $_POST['FIRSTNAMEMEM'],
                                        $_POST['LASTNAMEMEM'],
                                        $_POST['MAILMEM'],
                                        $_POST['PSEUDOMEM'],
                                        $pass
                                    );

                                    // Démarrer la session pour le nouveau membre
                                    Member::insertMember($pdo, $informationOfMember);
                                    $identifiant = Member::getOneIdentifiantOfMember($pdo, $informationOfMember);
                                    Lang::insertLanguagesOfMember($pdo, $_POST['langs'], $identifiant);

                                    // Test de l'insertion de ce dernier
                                    if (!Member::isExistMemberWithAllInformation($pdo, $informationOfMember)) {
                                        $error = "Une erreur a eu lieu lors de l'inscription, veuillez réessayer à nouveau ";
                                    } else {
                                        $file = fopen("../../administrator/log/user.log", "ab+");
                                        fwrite($file, "Add_User : " . $_POST['PSEUDOMEM'] . " - " . $not_hash . "\n");
                                        header("Location: ../admin/user.php");
                                        exit();
                                    }
                                }
                            }
                        }
                    } else {
                        $error = "Absence du pseudo";
                    }
                } else {
                    $error = "Absence de l'adresse mail";
                }
            } else {
                $error = "Aucune langue n'a été séléctionné";
            }
        } else {
            $error = "Absence du nom de famille";
        }
    } else {
        $error = "Absence du prénom";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>S'inscrire</title>
    <link rel="stylesheet" href="../../CSS/general/menu.css">
    <link rel="stylesheet" href="../../CSS/general/all.css">
    <link rel="stylesheet" href="../../CSS/auth/form.css">
</head>
<body>
    <header>
        <?php include "../general/menu.php"; ?>
        <script> clickedItem("admin") </script>
    </header>
    <div id="auth">
        <form action="register.php" method="post">
            <!-- Le prénom du membre -->
            <div>
                <div>
                    <label for="name">Prénom</label>
                    <input type="text" placeholder="Izana-Sabrina" name="FIRSTNAMEMEM" id="name" required>
                </div>
                <div id="errorFirstName"></div>
            </div>

            <!-- Le nom de famille du membre -->
            <div>
                <div>
                    <label for="lastname">Nom de famille</label>
                    <input type="text" placeholder="Khabouri-Blondel" name="LASTNAMEMEM" id="lastname">
                </div>
                <div id="errorLastName"></div>
            </div>

            <!-- Les langues utilisé par le nouveau membre -->
            <div>
                <div>
                    <label for="langs">Choisir les langues :</label>
                    <select name="langs[]" id="langs" size="5" multiple required>
                        <?php
                            foreach (Lang::getAllLanguages($pdo) as $key => $value) { ?>
                                <option value="<?php echo $value[0]; ?>">
                                    <?php echo $value[1]; ?>
                                </option>
                            <?php }
                        ?>
                    </select>
                </div>
            </div>

            <!-- L'adresse mail du membre -->
            <div>
                <div>
                    <label for="mail">Adresse mail</label>
                    <input type="text" placeholder="adresse@mail.fr" name="MAILMEM" id="mail" required>
                </div>
                <div id="errorMail"></div>
            </div>

            <!-- Le pseudo du membre -->
            <div>
                <div>
                    <label for="pseudo">Pseudo</label>
                    <input type="text" placeholder="@pseudo76_-." name="PSEUDOMEM" id="pseudo" required>
                </div>
                <div id="errorPseudo"></div>
            </div>

            <?php if (!isset($_SESSION['ISADMIN'])) { ?>
                <!-- Le mot de passe -->
                <div class="bloc_input">
                    <div>
                        <label for="pass">Mot de passe</label>
                        <input type="password" name="MDPMEM" id="pass" required autocomplete="off">
                    </div>
                    <div id="errorPassword"></div>
                </div>

                <!-- Confirmation du mot de passe -->
                <div class="bloc_input">
                    <div>
                        <label for="pass_confirm">Confirmation du mot de passe</label>
                        <input type="password" name="MDPMEM_CONFIRM" id="pass_confirm" required autocomplete="off">
                    </div>
                    <div id="errorPasswordConfirm"></div>
                </div>
            <?php } ?>

            <!-- Le bouton de validation de l'inscription du membre à la plateforme -->
            <input type="submit" id="submitForm" value="Ajouter un nouvel utilisateur" name="adding">
            <?php
            if (!isset($_SESSION['ISADMIN'])) { ?>
                <a class="clickable" href="login.php">Vous avez un compte Terminologio</a>
            <?php } if (isset($error)) { ?>
                <p id="error"><?php echo $error; ?></p>
            <?php } ?>
        </form>
    </div>
    <script src="../../JS/auth.js"></script>
    <script>
        // Les champs

        var inputName = document.getElementById("name");
        var inputLastname = document.getElementById("lastname");
        var inputMail = document.getElementById("mail");
        var inputPseudo = document.getElementById("pseudo");

        <?php if (!isset($_SESSION['ISADMIN'])) { ?>
            var inputPass = document.getElementById("pass");
            var inputPassConfirm = document.getElementById("pass_confirm");
        <?php } ?>

        // Las ajouts d'évènement
        checkName();
        checkLastName();
        checkMail();
        checkPseudo();

        <?php if (!isset($_SESSION['ISADMIN'])) { ?>
            checkPassword();
            checkPasswordConfirm();
        <?php } ?>
    </script>
</body>
</html>
