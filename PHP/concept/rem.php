<?php

include "../model/Database.php";
include "../model/Lang.php";
include "../model/Fields.php";
include "../model/Concept.php";

$pdo = Database::startSession();

if (!isset($_SESSION['IDMEM'])) {
    header("Location: ../concept/concepts.php");
    exit();
}

define("PATH", "../../assets/concepts/");

// Vérification
if (isset($_GET['state'])) {
    if ($_GET['state'] == 'favorite') {
        if (isset($_GET['member']) && !empty($_GET['member'])) {
            if (isset($_GET['concept']) && !empty($_GET['concept'])) {
                if (isset($_GET['dest']) && !empty($_GET['dest'])) {
                    $member = intval($_GET['member']);
                    $concept = intval($_GET['concept']);
                    Concept::removeMarkedConceptByMember($pdo, $member, $concept);
                    header("Location: " . $_GET['dest']);
                    exit();
                }
            }
        }
    }
    
    if ($_SESSION['ISADMIN']) {
        if ($_GET['state'] == 'concept') {
            if (isset($_GET['concept']) && !empty($_GET['concept'])) {
                $name = Concept::getConcept($pdo, intval($_GET['concept']));
                Fields::dissociateFieldOfConcept($pdo, intval($_GET['concept']));
                Concept::removeConcept($pdo, intval($_GET['concept']));
                if (!unlink(PATH . $name['REPRESENTATIONCON'])) {
                    $error = "L'image l'associant à ce concept n'a pas pu être supprimé";
                };
                header("Location: ../concept/concepts.php" . isset($error) ? "?error=" . $error : "");
                exit();
            }
        } else if ($_GET['state'] == 'component') {
            if (isset($_GET['comp']) && !empty($_GET['comp'])) {
                if (isset($_GET['concept']) && !empty($_GET['concept'])) {
                    $component = intval($_GET['comp']);
                    $concept = intval($_GET['concept']);
                    Concept::removeOneComponent($pdo, $concept, $component);
                    // echo "../concept/view.php?id" . $concept;
                    header("Location: ../concept/view.php?id=" . $concept);
                    exit();
                }
            }
        } else if ($_GET['state'] == 'translate') {
            if (isset($_GET['concept']) && !empty($_GET['concept'])) {
                if (isset($_GET['lang']) && !empty($_GET['lang'])) {
                    Concept::removeTranslate($pdo, intval($_GET['concept']), intval($_GET['lang']));
                    header("Location: ../concept/view.php?id=" . intval($_GET['concept']));
                    exit();
                }
            }
        } else if ($_GET['state'] == 'field') {
            if (isset($_GET['field']) && !empty($_GET['field'])) {
                Fields::removeFields($pdo, intval($_GET['field']));
                header('Location: ../admin/fields.php');
                exit();
            }
        } else if ($_GET['state'] == 'dissociate') {
            if (isset($_GET['field']) && !empty($_GET['field'])) {
                if (isset($_GET['concept']) && !empty($_GET['concept'])) {
                    Fields::dissociateFieldOfConcept($pdo, intval($_GET['field']), intval($_GET['concept']));
                    header('Location: ../concept/view.php?id=' . $_GET['concept']);
                    exit();
                }
            }
        } else if ($_GET['state'] == 'lang') {
            if (isset($_GET['lang']) && !empty($_GET['lang'])) {
                Lang::removeLang($pdo, intval($_GET['lang']));
                header('Location: ../admin/langs.php');
                exit();
            }
        }
    }
}

header("Location: ../concept/concepts.php");
exit();

?>
