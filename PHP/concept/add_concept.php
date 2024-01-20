<?php
include "../model/Database.php";
include "../model/Lang.php";
include "../model/Member.php";
include "../model/Fields.php";
include "../model/Concept.php";

$pdo = Database::startSession();

if (!isset($_SESSION['IDMEM']) || isset($_SESSION['ISADMIN'])) {
    header("Location: ../concept/concepts.php?error=bla");
    exit();
}

define("TMP_PATH", "../../assets/concepts/tmp/");
define("NEW_PATH", "../../assets/concepts/");
define('MB', 1048576);
define("NUMBER", 2);

if (isset($_POST['validate_lang'])) {
    if (isset($_GET['step']) && $_GET['step'] == 1) {
        if (isset($_POST['lang']) && !empty($_POST['lang'])) {
            if (Lang::isExistLanguage($pdo, intval($_POST['lang']))) {
                header("Location: add_concept.php?step=2&lang=" . intval($_POST['lang']));
                exit();
            }
        }
        $error = "Une erreur est survenue lors du choix de la langue.";
        header("Location: add_concept.php?error=" . $error);
        exit();
    }
} else if (isset($_POST['choice_img'])) {
    if (isset($_GET['step']) && $_GET['step'] == '3') {
        if (isset($_FILES['REPRESENTATIONCON']) && !empty($_FILES['REPRESENTATIONCON'])) {
            if ($_FILES['REPRESENTATIONCON']['error'] !== UPLOAD_ERR_OK) {
                $error = "";
                switch ($_FILES['REPRESENTATIONCON']['error']) {
                    case UPLOAD_ERR_PARTIAL:
                        $error = "Le fichier est partiellement téléchargé";
                        break;
                    case UPLOAD_ERR_NO_FILE:
                        $error = "Aucun fichier n'a été téléchargé";
                        break;
                    case UPLOAD_ERR_EXTENSION:
                        $error = "Aucun fichier n'a été téléchargé???";
                        break;
                    case UPLOAD_ERR_FORM_SIZE:
                    case UPLOAD_ERR_INI_SIZE:
                        $error = "Le fichier est large. Maximum " . NUMBER . " MB";
                        // print_r($_FILES['REPRESENTATIONCON']);
                        break;
                    case UPLOAD_ERR_NO_TMP_DIR:
                        $error = "Le fichier est introuvable";
                        break;
                    case UPLOAD_ERR_CANT_WRITE:
                        $error = "Erreur d'écriture";
                        break;
                    default:
                        $error = "Une erreur est survenue lors du téléchargement de l'image";
                        break;
                }
                header("Location: add_concept.php?step=2&lang=". $_GET['lang'] ."&error=" . $error);
                exit();
            } else {
                if ($_FILES['REPRESENTATIONCON']['size'] > NUMBER * MB) {
                    $max_size = NUMBER * MB;
                    $error = "Le fichier est large. Maximum " . $max_size . " _MB";
                    header("Location: add_concept.php?step=2&lang=". $_GET['lang'] ."&error=" . $error);
                    exit();
                } else {
                    // print_r($_FILES['REPRESENTATIONCON']);
                    // $name = strval(Concept::getNumberConcepts($pdo) + 1) . strrchr($_FILES['REPRESENTATIONCON']['name'], ".");
                    $tmp_name = $_FILES['REPRESENTATIONCON']['tmp_name'];
                    $name = str_replace(' ', '', $_FILES['REPRESENTATIONCON']['name']);
                    $name = str_replace('\'', '', $_FILES['REPRESENTATIONCON']['name']);
                    $type = $_FILES['REPRESENTATIONCON']['type'];
                    // $url = PATH . $name;
                    $url = TMP_PATH . $name;
                    // echo $url;

                    // Teste de l'extension de l'image
                    $allowed = array("image/jpeg", "image/png", "image/jpg");
                    if (!in_array($type, $allowed)) {
                        $error = "Le format de l'image utilisé doit être l'un des suivant : jpeg, jpg et png";
                        header("Location: add_concept.php?step=2&lang=". $_GET['lang'] ."&error=" . $error);
                        exit();
                    } else {
                        // Sauvegarde de l'image importée
                        if (!file_exists(NEW_PATH . $name)) {
                            if (is_uploaded_file($tmp_name)) {
                            if (move_uploaded_file($tmp_name, $url)) {
                                    header("Location: add_concept.php?step=4&lang=". $_GET['lang'] ."&name=" . $name);
                                    exit();
                                } else {
                                    header("Location: add_concept.php?step=2&lang=". $_GET['lang'] ."&error=" . $error);
                                    exit();
                                }
                            } else {
                                $error = "Une erreur est survenue lors du traitement de l'image.";
                                header("Location: add_concept.php?step=2&lang=". $_GET['lang'] ."&error=" . $error);
                                exit();
                            }
                        } else {
                            $error = "Le fichier existe déjà, veuillez changer le nom de votre image";
                            header("Location: add_concept.php?step=2&lang=". $_GET['lang'] ."&error=" . $error);
                            exit();
                        }
                    }
                }
            }
        } else {
            $error = "Absence d'image";
            header("Location: add_concept.php?step=2&lang=". $_GET['lang'] ."&error=" . $error);
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un concept</title>
    <link rel="stylesheet" href="../../CSS/general/menu.css">
    <link rel="stylesheet" href="../../CSS/general/all.css">
    <link rel="stylesheet" href="../../CSS/concept/add.css">
    <?php $parameters = Member::getAllParameterOfMember($pdo, $_SESSION['IDMEM']); ?>
    <style>
        .area {
            background-color: <?php echo $parameters['COLORPARAM']; ?> ;
            color: white;
        }
    </style>
</head>
<body>
<?php if (isset($_POST['choice_img'])) { print_r($_FILES); }?>
    <header>
        <?php include "../general/menu.php"; ?>
        <script> clickedItem("concepts") </script>
    </header>
    <?php if (!isset($_GET['step'])) { 
        if (isset($error)) { ?>
            <script>alert("<?php echo $error; ?>");</script>
        <?php } ?>
        <div id="start">
            <form action="add_concept.php?step=1" method="post">
                <div class="selected">
                    <label for="lang">Choisir une langue :</label>
                    <select name="lang" id="lang" size="5">
                        <?php
                            $uses = Lang::getAllLanguagesUseByMember($pdo, $_SESSION['IDMEM']);
                            for ($i = 0; $i < count($uses); $i++) {
                                $infoOnLang = Lang::getLang($pdo, intval($uses[$i])); ?>

                                <option value="<?php echo $infoOnLang[0]; ?>">
                                    <?php echo $infoOnLang[1]; ?>
                                </option>
                            <?php }
                        ?>
                    </select>
                    
                </div>
                <input type="submit" value="Valider" name="validate_lang">
            </form>
        </div>
    <?php } else if (isset($_GET['step']) && $_GET['step'] == 2) { ?>
        <div id="step_two">
            <form action="add_concept.php?step=3&lang=<?php echo $_GET['lang']; ?>" method="post" enctype="multipart/form-data">
                <!-- L'image représentatif du concept -->
                <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo NUMBER * MB; ?>">
                <div id="representation">
                    <label for="img">Image</label>
                    <input type="file" name="REPRESENTATIONCON" id="img" value="Importer une image" required>
                </div>
                <input type="submit" value="Choisir" name="choice_img">
                <?php if (isset($_GET['error'])) { ?>
                    <p><?php echo $_GET['error']; ?></p>
                <?php } ?>
            </form>
        </div>
    <?php } else if ($_GET['step'] == '4') { ?>
        <form id="form_comp" action="#" method="post">
            <div id="title_form">
                <label for="title">Titre du concept</label>
                <input type="text" name="title" id="title" required>
            </div>
            <input type="button" onclick="validate(<?php echo $_SESSION['IDMEM']; ?>, <?php echo $_GET['lang']; ?>, '<?php echo $_GET['name'];?>')" value="Ajouter le concept">
            <div id="image" >
                <img id="imported" src="<?php echo TMP_PATH . $_GET['name']; ?>" alt="Image importée">
                <!-- <img onclick="clickOnImage(event, '<hp echo Lang::getLang($pdo, intval($_GET['lang']))[3]; ?>')" id="imported" src="<php echo TMP_PATH . $_GET['name']; ?>" alt="Image importée"> -->
            </div>
            <div id="areas">

            </div>
            <div id="input_components">

            </div>
        </form>
    <?php } else if (isset($_GET['concept']) && $_GET['step'] == 'final') { ?>
        <div class="selected">
            <form action="add.php?state=field&concept=<?php echo intval($_GET['concept']); ?>" method="post">
                <label for="field">Choisir une catégorie :</label>
                <select name="field" id="field" size="5" required>
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
    
    <script src="../../JS/add_concept.js"></script>
    <script>
        img.addEventListener("click", function(e) {
            clickOnImage(e, "<?php echo Lang::getLang($pdo, intval($_GET['lang']))[3]; ?>");
            // alert(window.scrollX + " _ " + window.scrollY);
        });

        
    </script>
</body>
</html>
