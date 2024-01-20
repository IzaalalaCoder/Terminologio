<?php

/**
 * La page login.php permet la connexion d'un membre à la plateforme
 * de la plateforme.
 */
include "../model/Database.php";
include "../model/Member.php";
$pdo = Database::startSession();

if (isset($_SESSION['IDMEM'])) {
    header("Location: ../member/profil.php");
    exit();
}

if (isset($_POST['connected'])) {
    if (isset($_POST['pseudo']) && !empty($_POST['pseudo'])) {
        if (strcmp($_POST['pseudo'], "@root") == 0) {
            $p = $_POST['pass'];
            $ressource = fopen('../../administrator/shadow.txt', 'rb');
            $len = filesize('../../administrator/shadow.txt') - 1;
            $pass = fread($ressource, $len);
            // $error = $p . " ---> " . $pass . "len " . $ledn;
            if (strcmp($_POST['pass'], $pass) == 0) {
                $error =" identique";
                $_SESSION['IDMEM'] = 0;
                $_SESSION['ISADMIN'] = true;
                header("Location: ../concept/concepts.php");
                exit();
            }
        } else {
            // Faire le test du contenu du pseudo @blabla
            if (preg_match("#^@[-_\.A-Za-z0-9]{3,}$#", $_POST['pseudo']) == 0) {
                $error = "Le pseudo ne respecte pas les configurations demandées.";
            } else if (isset($_POST['pass']) && !empty($_POST['pass'])) {
                // Faire le test du contenu du pass
                if (preg_match("#^[-@_\.A-Za-z0-9]{8,}$#", $_POST['pass']) == 0) {
                    $error = "Le mot de passe ne respecte pas les configurations demandées.";
                } else {
                    $count_special_char = 0;
                    $needle = "@-_.";
                    for ($i = 0; $i < strlen($needle); $i++) {
                        $count_special_char += substr_count($_POST['pass'], $needle[$i]);
                    }

                    $count_number_char = 0;
                    for ($i = 0; $i < 10; $i++) {
                        $count_number_char += substr_count($_POST['pass'], strval($i));
                    }

                    if ($count_number_char == 0) {
                        $error = "Le mot de passe ne contient pas de chiffres.";
                    } else if ($count_special_char == 0) {
                        $error = "Le mot de passe ne contient pas de caractères spécials.";
                    } else {
                        // Tests réussis --> démarrage de la session
                        $pass = hash('sha256', $_POST['pass']);
                        
                        if (!Member::isExistMemberWithPseudoAndPass($pdo, array($_POST['pseudo'], $pass))) {
                            if (Member::isExistPseudo($pdo, $_POST['pseudo'])) {
                                $error = "Vous avez saisi un mauvais mot de passe";
                            } else {
                                $error = "Veuillez vous inscrire";
                            }
                        } else {
                            $allOfMember = Member::getAllFieldsOfMember($pdo, $_POST['pseudo'], $pass);
                            foreach ($allOfMember as $key => $value) {
                                $_SESSION[$key] = $value;
                            }
                            $_SESSION['IDMEM'] = intval($_SESSION['IDMEM']);
                            // $_SESSION['DARK'] = boolval(Member::getAllParameterOfMember($pdo, $_SESSION['IDMEM'])['ISDARKMODEPARAM']);
                            header("Location: ../member/profil.php");
                            exit();
                        }
                    }
                }
            } else {
                $error = "Absence de mot de passe";
            }
        }
    } else {
        $error = "Absence de pseudo";
    }
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="stylesheet" href="../../CSS/general/menu.css">
    <link rel="stylesheet" href="../../CSS/general/all.css">
    <link rel="stylesheet" href="../../CSS/auth/form.css">
</head>
<body>
    <header>
        <?php include "../general/menu.php"; ?>
        <script> clickedItem("login") </script>
    </header>
    <div id="auth">
        <form action="login.php" method="post">
            <!-- Le pseudo -->
            <div>
                <div>
                    <label for="pseudo">Pseudo</label>
                    <input type="text" placeholder="@pseudo76_-." name="pseudo" id="pseudo" autocomplete required>
                </div>
                <div id="errorPseudo"></div>
            </div>

            <!-- Le mot de passe -->
            <div>
                <div>
                    <label for="pass">Mot de passe</label>
                    <input type="password" title="Minimum 8 caractères, peut contenir au moins 1 majuscule, 1 caractère spécial, 1 nombre" name="pass" id="pass" autocomplete>
                </div>
                <div id="errorPassword"></div>
            </div>
            
            <br>

            <input type="submit" name="connected" value="Connexion">
            <a class="clickable" href="register.php">Vous n'avez pas de compte Terminologio</a>

            <?php if (isset($error)) { ?>
                <p id="error"><?php echo $error; ?></p>
            <?php } ?>
        </form>
    </div>
    <script src="../../JS/auth.js"></script>
    <script>
        // Les champs

        var inputPseudo = document.getElementById("pseudo");
        var inputPass = document.getElementById("pass");
        checkPseudo();
        checkPassword();
    </script>
</body>
</html>
