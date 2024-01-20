<?php

class Fields {
    public static function addFields(PDO $pdo, string $field): void {
        $request_str = "INSERT INTO CATEGORIE(NAMECATEGORIE) VALUES(?)";
        $request = $pdo->prepare($request_str);
        $request->execute(array($field));
    }

    public static function removeFields(PDO $pdo, int $field): void {
        Fields::removeFieldsOfAssociation($pdo, $field);
        $request_str = "DELETE FROM CATEGORIE WHERE IDCATEGORIE = ?";
        $request = $pdo->prepare($request_str);
        $request->execute(array($field));
    }

    public static function searchInFields(PDO $pdo, int $field): array {
        $request_str = "SELECT CF_CONCEPT FROM COMING_FROM WHERE CF_CATEGORIE = ?";
        $request = $pdo->prepare($request_str);
        $request->execute(array($field));
        $concepts = array();
        while ($c = $request->fetch()) {
            array_push($concepts, $c['CF_CONCEPT']);
        }
        return $concepts;
    }

    public static function getFieldAssociateConcept(PDO $pdo, int $concept): int {
        $request_str = "SELECT CF_CATEGORIE FROM COMING_FROM WHERE CF_CONCEPT = ?";
        $request = $pdo->prepare($request_str);
        $request->execute(array($concept));
        if ($request->rowCount() == 1) {
            $r = $request->fetch();
            return intval($r['CF_CATEGORIE']);
        }
        return -1;
    }

    public static function getAllFields(PDO $pdo): array {
        $request_str = "SELECT * FROM CATEGORIE";
        $request = $pdo->prepare($request_str);
        $request->execute(array());
        $fields = array();
        while ($r = $request->fetch()) {
            array_push($fields, array($r['IDCATEGORIE'], $r['NAMECATEGORIE']));
        }
        return $fields;
    }

    public static function getField(PDO $pdo, int $id): array {
        $request_str = "SELECT * FROM CATEGORIE WHERE IDCATEGORIE = ?";
        $request = $pdo->prepare($request_str);
        $request->execute(array($id));
        $arr = array();
        if ($request->rowCount() == 0) {
            return $arr;
        }
        $r = $request->fetch();
        $arr['IDCATEGORIE'] = $r['IDCATEGORIE'];
        $arr['NAMECATEGORIE'] =$r['NAMECATEGORIE'];
        return $arr;
    }

    public static function associateFieldAndConcept(PDO $pdo, int $field, int $concept): void {
        $request_str = "INSERT INTO COMING_FROM VALUES(?, ?)";
        $request = $pdo->prepare($request_str);
        $request->execute(array($field, $concept));
    }

    // Supprime l'association d'un concept à une catégorie
    public static function dissociateFieldOfConcept(PDO $pdo, int $field, int $concept): void {
        $request_str = "DELETE FROM COMING_FROM WHERE CF_CATEGORIE = ? AND CF_CONCEPT = ?";
        $request = $pdo->prepare($request_str);
        $request->execute(array($field, $concept));
    }

    public static function isNotExistField(PDO $pdo, string $name): bool {
        $request_str = "SELECT * FROM CATEGORIE WHERE NAMECATEGORIE = ?";
        $request = $pdo->prepare($request_str);
        $request->execute(array($name));
        return $request->rowCount() == 0;
    }

    public static function isAssociatedWithField(PDO $pdo, int $concept): bool {
        $request_str = "SELECT * FROM COMING_FROM WHERE CF_CONCEPT = ?";
        $request = $pdo->prepare($request_str);
        $request->execute(array($concept));
        return $request->rowCount() == 1;
    }

    public static function removeFieldsOfAssociation(PDO $pdo, int $field): void {
        $request_str = "DELETE FROM COMING_FROM WHERE CF_CATEGORIE = ?";
        $request = $pdo->prepare($request_str);
        $request->execute(array($field));
    }
}

?>
