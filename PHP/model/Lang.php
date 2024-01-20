<?php

class Lang {

    public static function getAllLanguages(PDO $pdo): array {
        $request_str = "SELECT * FROM LANG";
        $request = $pdo->prepare($request_str);
        $request->execute(array());

        $langs = array();
        if ($request->rowCount() == 0) {
            return $langs;
        } else {
            while ($r = $request->fetch()) {
                $l = array($r['IDLANG'], $r['NAMELANG'], $r['CODELANG'], $r['COMPLANG']);
                array_push($langs, $l);
            }
        }
        return $langs;
    }

    public static function getLang(PDO $pdo, int $i): array {
        // echo $i;
        $request_str = "SELECT * FROM LANG WHERE IDLANG = ?";
        $request = $pdo->prepare($request_str);
        $request->execute(array($i));
        $informationOfLang = array();
        if ($request->rowCount() == 1) {
            $r = $request->fetch();
            array_push($informationOfLang, $r['IDLANG'], $r['NAMELANG'], $r['CODELANG'], $r['COMPLANG']);
        }
        // print_r($informationOfLang);
        return $informationOfLang;
    }

    public static function getNumberLanguages(PDO $pdo): int {
        $request_str = "SELECT * FROM LANG";
        $request = $pdo->prepare($request_str);
        $request->execute(array());

        return $request->rowCount();
    }

    public static function isExistLanguage(PDO $pdo, int $i): bool {
        $request_str = "SELECT * FROM LANG WHERE IDLANG = ?";
        $request = $pdo->prepare($request_str);
        $request->execute(array($i));
        
        return $request->rowCount() == 1;
    }

    public static function isExistLanguageWithName(PDO $pdo, string $name): bool {
        $request_str = "SELECT * FROM LANG WHERE NAMELANG = ?";
        $request = $pdo->prepare($request_str);
        $request->execute(array($name));
        
        return $request->rowCount() == 1;
    }

    public static function insertLanguagesOfMember(PDO $pdo, array $langs, int $i): void {
        // print_r($langs);
        for ($index = 0; $index < count($langs); $index++) {
            $request_str = "INSERT INTO USES(MEMBERUSES, LANGUSES) VALUES (?, ?)";
            $request = $pdo->prepare($request_str);
            $request->execute(array($i, $langs[$index]));
        }
    }

    public static function addLang(PDO $pdo, string $name, string $code, string $title): void {
        $request_str = "INSERT INTO LANG(NAMELANG, CODELANG, COMPLANG) VALUES(?,?,?)";
        $request = $pdo->prepare($request_str);
        $request->execute(array($name, $code, $title));
    }

    public static function removeLang(PDO $pdo, int $lang): void {
        Lang::removeLangUses($pdo, $lang);
        $request_str = "DELETE FROM LANG WHERE IDLANG = ?";
        // echo "DELETE FROM LANG WHERE IDLANG = " . $lang;
        $request = $pdo->prepare($request_str);
        $request->execute(array($lang));
    }

    public static function removeLangUses(PDO $pdo, int $lang): void {
        $request_str = "DELETE FROM USES WHERE LANGUSES = ?";
        $request = $pdo->prepare($request_str);
        $request->execute(array($lang));
    }

    public static function removeLanguagesOfMember(PDO $pdo, int $i): void {
        $request_str = "DELETE FROM USES WHERE MEMBERUSES = ?";
        $request = $pdo->prepare($request_str);
        $request->execute(array($i));
    }

    public static function getAllLanguagesUseByMember(PDO $pdo, int $i): array {
        $request_str = "SELECT LANGUSES FROM USES WHERE MEMBERUSES = ?";
        $request = $pdo->prepare($request_str);
        $request->execute(array($i));

        $langs = array();
        if ($request->rowCount() == 0) {
            return $langs;
        } else {
            while ($r = $request->fetch()) {
                array_push($langs, $r['LANGUSES']);
            }
        }
        return $langs;
    }

    public static function canTranslateWithoutUseDefaultLang(PDO $pdo, int $member, int $default): bool {
        $request_str = "SELECT * FROM USES WHERE MEMBERUSES = ? AND LANGUSES != ?";
        $request = $pdo->prepare($request_str);
        $request->execute(array($member, $default));
        return $request->rowCount() > 0;
    }


    public static function isUseByMember(PDO $pdo, int $member, int $lang): bool {
        $request_str = "SELECT * FROM USES WHERE MEMBERUSES = ? AND LANGUSES = ?";
        $request = $pdo->prepare($request_str);
        $request->execute(array($member, $lang));
        return $request->rowCount() == 1;
    }

    public static function updateLanguagesOfMember(PDO $pdo, int $member, array $langs) {
        foreach ($langs as $l) {
            if (!Lang::isUseByMember($pdo, $member, $l)) {
                $request_str = "INSERT INTO USES VALUES(?, ?)";
                $request = $pdo->prepare($request_str);
                $request->execute(array($member, $l));
            }
        }

        foreach (Lang::getAllLanguagesUseByMember($pdo, $member) as $lang) {
            $l = intval($lang);
            if (!in_array($l, $langs)) {
                $request_str = "DELETE FROM USES WHERE MEMBERUSES = ? AND LANGUSES = ?";
                $request = $pdo->prepare($request_str);
                $request->execute(array($member, $l));
            }
        }
    }
}

?>
