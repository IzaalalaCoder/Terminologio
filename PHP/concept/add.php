<?php
include "../model/Database.php";
include "../model/Lang.php";
include "../model/Fields.php";
include "../model/Concept.php";

$pdo = Database::startSession();

define("TMP_PATH", "../../assets/concepts/tmp/");
define("NEW_PATH", "../../assets/concepts/");

if (!isset($_SESSION['IDMEM'])) {
    header("Location: ../concept/concepts.php");
    exit();
}

// Vérification

if (isset($_GET['state'])) {
    
    // Ajouter des concepts
    if ($_GET['state'] == 'concept') {
        if (isset($_GET['member']) && !empty($_GET['member'])) {
            $member = intval($_GET['member']);
            if (isset($_GET['lang']) && !empty($_GET['lang'])) {
                $lang = intval($_GET['lang']);
                if (isset($_GET['name']) && !empty($_GET['name'])) {
                    $url = NEW_PATH . $_GET['name'];
                    if (Concept::getIdOfConcept($pdo, $url) == -1) {
                        if (isset($_GET['title']) && !empty($_GET['title'])) {
                            $title = htmlspecialchars($_GET['title']);
                            if (isset($_GET['comps']) && !empty($_GET['comps'])) {
                                if (isset($_GET['count']) && !empty($_GET['count'])) {
                                    if (rename(TMP_PATH . $_GET['name'], $url)) {
                                        Concept::insertConcept($pdo, $title, $url, $lang, $member, $_GET['comps']);
                                        header('Location: add_concept.php?step=final&concept=' . Concept::getIdOfConcept($pdo, $url));
                                        exit();
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    
    // Ajouter des favoris
    } else if ($_GET['state'] == 'favorite') {
        if (isset($_GET['member']) && !empty($_GET['member'])) {
            if (isset($_GET['concept']) && !empty($_GET['concept'])) {
                if (isset($_GET['dest']) && !empty($_GET['dest'])) {
                    $member = intval($_GET['member']);
                    $concept = intval($_GET['concept']);
                    Concept::addMarkedConceptByMember($pdo, $member, $concept);
                    header('Location: ' . $_GET['dest']);
                    exit();
                }
            }
        }

    // Ajouter des traductions
    } else if ($_GET['state'] == 'translate') {
        if (isset($_GET['concept']) && !empty($_GET['concept'])) {
            if (isset($_GET['lang']) && !empty($_GET['lang'])) {
                if (isset($_GET['update']) && !empty($_GET['update'])) {
                    $lang = Lang::getLang($pdo, intval($_GET['lang']));

                    Concept::addTranslate($pdo, intval($_GET['concept']), $lang[0], $lang[3], $_GET['update']);
                    header("Location: ../concept/view.php?id=" . $_GET['concept']);
                    exit();
                }
            }
        }

    // Ajouter des catégories
    } else if ($_GET['state'] == 'field') {
        if (isset($_GET['concept']) && !empty($_GET['concept'])) {
            if (isset($_POST['field']) && !empty($_POST['field'])) {
                Fields::associateFieldAndConcept($pdo, intval($_POST['field']), intval($_GET['concept']));
                if (isset($_GET['dest']) && !empty($_GET['dest'])) {
                    header('Location: ../concept/' . $_GET['dest'] . '?id=' . $_GET['concept']);
                    exit();
                }
                header('Location: concepts.php');
                exit();
            }
        }
    }
}

header("Location: ../concept/concepts.php");
exit();

?>
