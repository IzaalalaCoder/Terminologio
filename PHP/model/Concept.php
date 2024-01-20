<?php

class Concept {

    public static function getAllConcepts(PDO $pdo): array {
        $request_str = "SELECT * FROM CONCEPT ORDER BY IDCON DESC";
        $request = $pdo->prepare($request_str);
        $request->execute(array());

        $concepts = array();
        if ($request->rowCount() == 0) {
            return $concepts;
        } else {
            while ($r = $request->fetch()) {
                $c = array($r['IDCON'], $r['TITLECON'], $r['REPRESENTATIONCON'], $r['DEFAULTLANGIDCON'], $r['AUTHORCON']);
                array_push($concepts, $c);
            }
        }
        return $concepts;
    }

    public static function getConcept(PDO $pdo, int $id): array {
        $request_str = "SELECT * FROM CONCEPT WHERE IDCON = ?";
        $request = $pdo->prepare($request_str);
        $request->execute(array($id));

        $c = array();
        if ($request->rowCount() != 1) {
            return $c;
        }
        $r = $request->fetch();
        $c['IDCON'] = $r['IDCON'];
        $c['TITLECON'] = $r['TITLECON'];
        $c['REPRESENTATIONCON'] = $r['REPRESENTATIONCON'];
        $c['DEFAULTLANGIDCON'] = $r['DEFAULTLANGIDCON'];
        $c['AUTHORCON'] = $r['AUTHORCON'];
        
        return $c;
    }

    public static function getAllConceptsRealizedByMember(PDO $pdo, int $member): array {
        $request_str = "SELECT * FROM CONCEPT WHERE AUTHORCON = ? ORDER BY IDCON DESC";
        $request = $pdo->prepare($request_str);
        $request->execute(array($member));

        $concepts = array();
        if ($request->rowCount() == 0) {
            return $concepts;
        } else {
            while ($r = $request->fetch()) {
                $c = array($r['IDCON'], $r['TITLECON'], $r['REPRESENTATIONCON'], $r['DEFAULTLANGIDCON'], $r['AUTHORCON']);
                array_push($concepts, $c);
            }
        }
        return $concepts;
    }

    public static function getNumberConcepts(PDO $pdo): int {
        $request_str = "SELECT * FROM CONCEPT";
        $request = $pdo->prepare($request_str);
        $request->execute(array());

        return $request->rowCount();
    }

    public static function removeAllComponentsOfConcept(PDO $pdo, int $id) {
        $request_str = "DELETE FROM COMPONENT WHERE DEFINEDTERM = ?";
        $request = $pdo->prepare($request_str);
        $request->execute(array($id));
    }
    
    public static function removeOneComponent(PDO $pdo, int $concept, int $component): void {
        $request_str = "DELETE FROM COMPONENT WHERE DEFINEDTERM = ? AND IDCOMPONENT = ?";
        $request = $pdo->prepare($request_str);
        $request->execute(array($concept, $component));
    }

    public static function removeAllConceptRealizedByMember(PDO $pdo, int $id) {

        // Suppression de chaque composants des concepts réalisé par le membre
        $concepts = Concept::getAllConcepts($pdo);
        for ($i = 0; $i < count($concepts); $i++) {
            Concept::removeAllComponentsOfConcept($pdo, intval($concepts[$i][0]));
        }
        
        $request_str = "DELETE FROM CONCEPT WHERE AUTHORCON = ?";
        $request = $pdo->prepare($request_str);
        $request->execute(array($id));
    }

    public static function removeAllTranslateOnConcept(PDO $pdo, int $id) {
        foreach (Concept::getAllComponentsOfConcept($pdo, $id) as $component) {
            $request_str = "DELETE FROM TRANSLATE WHERE COMPONENTIDTRANS = ?";
            $request = $pdo->prepare($request_str);
            $request->execute(array(intval($component[0])));
        }
    }

    public static function removeAllConceptMarkedByMember(PDO $pdo, int $id) {
        $request_str = "DELETE FROM MARKED WHERE MEMBERIDMARK = ?";
        $request = $pdo->prepare($request_str);
        $request->execute(array($id));
    }

    public static function addMarkedConceptByMember(PDO $pdo, int $member, int $concept): void {
        $request_str = "INSERT INTO MARKED VALUES (?, ?)";
        $request = $pdo->prepare($request_str);
        $request->execute(array($concept, $member));
    }

    public static function removeMarkedConceptByMember(PDO $pdo, int $member, int $concept): void {
        $request_str = "DELETE FROM MARKED WHERE CONCEPTIDMARK = ? AND MEMBERIDMARK = ?";
        $request = $pdo->prepare($request_str);
        $request->execute(array($concept, $member));
    }

    public static function getNumberMarkedConcepts(PDO $pdo, int $member): int {
        $request_str = "SELECT * FROM MARKED WHERE MEMBERIDMARK = ?";
        $request = $pdo->prepare($request_str);
        $request->execute(array($member));
        return $request->rowCount();
    }

    public static function getAllMarkedConcepts(PDO $pdo, int $member): array {
        $request_str = "SELECT * FROM CONCEPT, MARKED WHERE IDCON = CONCEPTIDMARK AND MEMBERIDMARK = ?";
        $request = $pdo->prepare($request_str);
        $request->execute(array($member));

        $concepts = array();
        if ($request->rowCount() == 0) {
            return $concepts;
        } else {
            while ($r = $request->fetch()) {
                $c = array($r['IDCON'], $r['TITLECON'], $r['REPRESENTATIONCON'], $r['DEFAULTLANGIDCON'], $r['AUTHORCON']);
                array_push($concepts, $c);
            }
        }
        return $concepts;
    }
    
    public static function isMarkedConceptByMember(PDO $pdo, int $member, int $concept): bool {
        $request_str = "SELECT * FROM MARKED WHERE CONCEPTIDMARK = ? AND MEMBERIDMARK = ?";
        $request = $pdo->prepare($request_str);
        $request->execute(array($concept, $member));
        return $request->rowCount() == 1;
    }

    public static function insertConcept(PDO $pdo, string $title, string $url, int $lang, int $member, string $components): void {
        $request_str = "INSERT INTO CONCEPT(TITLECON, REPRESENTATIONCON, DEFAULTLANGIDCON, AUTHORCON) VALUES (?,?,?,?)";
        $request = $pdo->prepare($request_str);
        $request->execute(array($title, $url, $lang, $member));
        $comps = explode("|", $components);
        array_pop($comps);
        for ($i = 0; $i < count($comps); $i++) {
            $c = explode("_", $comps[$i]);
            // echo "<br>";
            // print_r($c);
            Concept::insertComponents($pdo, Concept::getIdOfConcept($pdo, $url), $c);
        }
    }

    public static function getIdOfConcept(PDO $pdo, string $url): int {
        $request_str = "SELECT IDCON FROM CONCEPT WHERE REPRESENTATIONCON LIKE ?";
        $request = $pdo->prepare($request_str);
        $request->execute(array($url));
        if ($request->rowCount() != 1) {
            return -1;
        }
        $r = $request->fetch();
        return intval($r['IDCON']);
    }

    public static function insertComponents(PDO $pdo, int $conceptId, array $c): void {
        $request_str = "INSERT INTO COMPONENT(POSXCOMP, POSYCOMP, TERMINOLOGYCOMP, DEFINEDTERM) VALUES (?,?,?,?)";
        $request = $pdo->prepare($request_str);

        $positionX = intval($c[0]);
        $positionY = intval($c[1]);
        $term = $c[2];

        $request->execute(array($positionX, $positionY, $term, $conceptId));
        // $request->debugDumpParams();
        // echo $request->
    }

    public static function getAllComponentsOfConcept(PDO $pdo, int $concept): array {
        $request_str = "SELECT * FROM COMPONENT WHERE DEFINEDTERM = ? ORDER BY IDCOMPONENT";
        $request = $pdo->prepare($request_str);
        $request->execute(array($concept));

        $comps = array();
        if ($request->rowCount() == 0) {
            return $comps;
        } else {
            while ($r = $request->fetch()) {
                $c = array($r['IDCOMPONENT'], $r['POSXCOMP'], $r['POSYCOMP'], $r['TERMINOLOGYCOMP']);
                array_push($comps, $c);
            }
        }
        return $comps;
    }

    public static function removeAllMarkedByUsers(PDO $pdo, int $id) {
        $request_str = "DELETE FROM MARKED WHERE CONCEPTIDMARK = ?";
        $request = $pdo->prepare($request_str);
        $request->execute(array($id));
    }

    public static function removeConcept(PDO $pdo, int $id) {
        Concept::removeAllMarkedByUsers($pdo, $id);
        Concept::removeAllTranslateOnConcept($pdo, $id);
        Concept::removeAllComponentsOfConcept($pdo, $id);

        $request_str = "DELETE FROM CONCEPT WHERE IDCON = ?";
        $request = $pdo->prepare($request_str);
        $request->execute(array($id));
    }

    public static function getCurrentTerminology(PDO $pdo, int $component): string {
        $request_str = "SELECT TERMINOLOGYCOMP FROM COMPONENT WHERE IDCOMPONENT = ?";
        $request = $pdo->prepare($request_str);
        $request->execute(array($component));
        if ($request->rowCount() == 1) {
            $r = $request->fetch();
            return $r['TERMINOLOGYCOMP'];
        }
        return "";
    }

    public static function updateComponent(PDO $pdo, int $component, string $term) {
        $request_str = "UPDATE COMPONENT SET TERMINOLOGYCOMP = ? WHERE IDCOMPONENT = ?";
        // echo "UPDATE COMPONENT SET TERMINOLOGYCOMP = ". $term ." WHERE IDCOMPONENT = ". $component ;
        $request = $pdo->prepare($request_str);
        $request->execute(array($term, $component));
    }

    public static function updateAllComponents(PDO $pdo, string $updating) {
        $update = explode("|", $updating);
        array_pop($update);
        for ($i = 0; $i < count($update); $i++) {
            $u = explode("_", $update[$i]);
            $comp = intval(substr($u[0], 1));
            $term = $u[1];
            if (strcmp($term, Concept::getCurrentTerminology($pdo, $comp)) != 0) {
                Concept::updateComponent($pdo, $comp, $term);
            }
        }
    }

    public static function getDefaultLang(PDO $pdo, int $id): int {
        $request_str = "SELECT DEFAULTLANGIDCON FROM CONCEPT WHERE IDCON = ?";
        $request = $pdo->prepare($request_str);
        $request->execute(array($id));
        if ($request->rowCount() == 1) {
            $r = $request->fetch();
            return intval($r['DEFAULTLANGIDCON']);
        }
        return -1;
    }

    public static function getCurrentTranslate(PDO $pdo, int $component, int $lang): string {
        $request_str = "SELECT TRANSLATETERM FROM TRANSLATE WHERE COMPONENTIDTRANS = ? AND LANGIDTRANS = ?";
        $request = $pdo->prepare($request_str);
        $request->execute(array($component, $lang));
        if ($request->rowCount() == 1) {
            $r = $request->fetch();
            return $r['TRANSLATETERM'];
        }
        return "";
    }

    public static function ifConceptIsTranslate(PDO $pdo, int $concept, int $lang): bool {
        $components = Concept::getAllComponentsOfConcept($pdo, $concept);
        $request_str = "SELECT * FROM TRANSLATE WHERE LANGIDTRANS = ? AND COMPONENTIDTRANS = ?";
        $request = $pdo->prepare($request_str);
        $request->execute(array($lang, $components[0][0]));
        return $request->rowCount() == 1;
    }

    public static function updateTranslate(PDO $pdo, int $component, int $lang, string $term): void {
        $request_str = "UPDATE TRANSLATE SET TRANSLATETERM = ? WHERE LANGIDTRANS = ? AND COMPONENTIDTRANS = ?";
        echo "<br> UPDATE TRANSLATE SET TRANSLATETERM = \"". $term ."\" WHERE LANGIDTRANS = ". $lang ." AND COMPONENTIDTRANS =". $component;
        $request = $pdo->prepare($request_str);
        $request->execute(array($term, $lang, $component));
    }

    public static function insertTranslate(PDO $pdo, int $lang, int $component, string $term): void {
        $request_str = "INSERT INTO TRANSLATE(LANGIDTRANS, COMPONENTIDTRANS, TRANSLATETERM) VALUES(?,?,?)";
        // echo "INSERT INTO TRANSLATE(LANGIDTRANS, COMPONENTIDTRANS, TRANSLATETERM) VALUES(" . $lang ." ," . $component . ", " . $term . ")";
        $request = $pdo->prepare($request_str);!
        $request->execute(array($lang, $component, $term));
    }

    public static function addTranslate(PDO $pdo, int $concept, int $lang, string $title, string $translate): void {
        $translating = Concept::ifConceptIsTranslate($pdo, $concept, $lang);
        echo $translating ? "true" : "false";
        $translates = explode("|", $translate);
        array_pop($translates);
        echo count($translates);
        for ($i = 0; $i < count($translates); $i++) {
            $t = explode("_", $translates[$i]);
            $comp = intval(substr($t[0], 1));
            $term = $t[1];
            if ($translating) {
                // update
                if (strcmp($term, Concept::getCurrentTranslate($pdo, $comp, $lang)) != 0) {
                    Concept::updateTranslate($pdo, $comp, $lang, $term);
                }
                // Conept::updateTranslating()
            } else {
                // insert
                $changing = strcmp(Concept::getComponent($pdo, $comp)['TERMINOLOGYCOMP'], $term) != 0;
                Concept::insertTranslate($pdo, $lang, $comp, $changing ? $term : $title . " " . ($i + 1));
            }
        }
    }

    public static function getComponent(PDO $pdo, int $component): array {
        $request_str = "SELECT * FROM COMPONENT WHERE IDCOMPONENT = ?";
        $request = $pdo->prepare($request_str);
        $request->execute(array($component));

        $c = array();
        if ($request->rowCount() != 1) {
            return $c;
        }
        $r = $request->fetch();
        $c['IDCOMPONENT'] = $r['IDCOMPONENT'];
        $c['POSXCOMP'] = $r['POSXCOMP'];
        $c['POSYCOMP'] = $r['POSYCOMP'];
        $c['TERMINOLOGYCOMP'] = $r['TERMINOLOGYCOMP'];
        $c['DEFINEDTERM'] = $r['DEFINEDTERM'];
        
        return $c;
    }
    
    public static function getAllTranslateOnConcept(PDO $pdo, int $concept): array {
        $langs = array();
        $components = Concept::getAllComponentsOfConcept($pdo, $concept);
        $request_str = "SELECT DISTINCT LANGIDTRANS FROM TRANSLATE WHERE COMPONENTIDTRANS = ?";
        $request = $pdo->prepare($request_str);
        $request->execute(array($components[0][0]));
        if ($request->rowCount() != 0) {
            while ($r = $request->fetch()) {
                array_push($langs, intval($r['LANGIDTRANS']));
            }
        }
        return $langs;
    }

    public static function getTranslateOnConceptInLang(PDO $pdo, int $concept, int $lang): array {
        $translates = array();
        foreach (Concept::getAllComponentsOfConcept($pdo, $concept) as $component) {
            // print_r($component);
            $request_str = "SELECT * FROM TRANSLATE WHERE LANGIDTRANS = ? AND COMPONENTIDTRANS = ?";
            // echo "SELECT * FROM TRANSLATE WHERE LANGIDTRANS = ". $lang ." AND COMPONENTIDTRANS = ". $component[0];
            $request = $pdo->prepare($request_str);
            $request->execute(array($lang, $component[0]));
            $r = $request->fetch();
            // print_r($r);
            array_push($translates, array($r['COMPONENTIDTRANS'], $r['TRANSLATETERM']));
        }
        return $translates;
    }

    public static function removeTranslate(PDO $pdo, int $concept, int $lang) {
        foreach (Concept::getAllComponentsOfConcept($pdo, $concept) as $component) {
            $request_str = "DELETE FROM TRANSLATE WHERE COMPONENTIDTRANS = ? AND LANGIDTRANS = ?";
            // echo "DELETE FROM TRANSLATE WHERE COMPONENTIDTRANS = ". $concept ." AND LANGIDTRANS = " . $lang;
            $request = $pdo->prepare($request_str);
            // print_r($component);
            $request->execute(array(intval($component[0]), $lang));
        }
    }

    public static function langIsUseByConcepts(PDO $pdo, int $lang): bool {
        // Vérification dans les concepts
        $request_str = "SELECT * FROM CONCEPT WHERE DEFAULTLANGIDCON = ?";
        $request = $pdo->prepare($request_str);
        $request->execute(array($lang));

        if ($request->rowCount() > 0) {
            return true;
        }

        // Vérification dans les traductions
        $request_str = "SELECT * FROM TRANSLATE WHERE LANGIDTRANS = ?";
        $request = $pdo->prepare($request_str);
        $request->execute(array($lang));
        
        return $request->rowCount() > 0;
    }

    public static function searchConcept(PDO $pdo, string $search): array {
        // Recherche du concept via le titre
        $request_str = "SELECT * FROM CONCEPT WHERE TITLECON LIKE '%". $search ."%'";
        $request = $pdo->prepare($request_str);
        $request->execute(array($search));

        $concepts = array();
        if ($request->rowCount() != 0) {
            while ($r = $request->fetch()) {
                // $c = array($r['IDCON'], $r['TITLECON'], $r['REPRESENTATIONCON'], $r['DEFAULTLANGIDCON'], $r['AUTHORCON']);
                array_push($concepts, Concept::getConcept($pdo, $r['IDCON']));
            }
        }

        // Recherche du concept via les composants
        $request_str = "SELECT DEFINEDTERM FROM COMPONENT WHERE TERMINOLOGYCOMP LIKE '%". $search ."%')";
        $request = $pdo->prepare($request_str);
        $request->execute(array($search));

        if ($request->rowCount() != 0) {
            while ($r = $request->fetch()) {
                if (!Concept::existConcept(intval($r['DEFINEDTERM']), $concepts)) {
                    // $concept = Concept::getConcept($pdo, $r['DEFINEDTERM']);
                    // $c = array($r['IDCON'], $r['TITLECON'], $r['REPRESENTATIONCON'], $r['DEFAULTLANGIDCON'], $r['AUTHORCON']);
                    array_push($concepts, Concept::getConcept($pdo, $r['DEFINEDTERM']));
                }
            }
        }

        // Recherche du concept via la traduction de ces composants
        $request_str = "SELECT COMPONENTIDTRANS FROM TRANSLATE WHERE TRANSLATETERM LIKE '%". $search ."%'";
        $request = $pdo->prepare($request_str);
        $request->execute(array($search));

        if ($request->rowCount() != 0) {
            while ($r = $request->fetch()) {
                $component = Concept::getComponent($pdo, $r['COMPONENTIDTRANS']);
                $concept = Concept::getConcept($pdo, $component['DEFINEDTERM']);
                if (!Concept::existConcept($component['DEFINEDTERM'], $concepts)) {
                    // $c = array($r['IDCON'], $r['TITLECON'], $r['REPRESENTATIONCON'], $r['DEFAULTLANGIDCON'], $r['AUTHORCON']);
                    array_push($concepts, $concept);
                }
            }
        }
        return $concepts;
    }

    public static function existConcept(int $concept, array $concepts): bool {
        foreach ($concepts as $c) {
            // print_r($c);
            if ($c['IDCON'] == $concept) {
                return true;
            }
        }
        return false;
    }
}

?>
