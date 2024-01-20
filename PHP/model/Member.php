<?php

/**
 * Cette classe permet la gestion de la base de donnée de terminologio
 */
define('PASS_LEN_MAX', 8);

class Member {

    /**
     * Retourne les informations liées au membre dont le pseudo vaut pseudo et que le mot de passe vaut password
     */

     public static function getAllMember(PDO $pdo): array {
        $request_str = "SELECT * FROM MEMBER";
        $request = $pdo->prepare($request_str);
        $request->execute(array());

        $members = array();
        if ($request->rowCount() == 0) {
            return $members;
        } else {
            while ($r = $request->fetch()) {
                $m = array($r['IDMEM'], $r['FIRSTNAMEMEM'], $r['LASTNAMEMEM'], $r['MAILMEM'], $r['PSEUDOMEM']);
                array_push($members, $m);
            }
        }
        // print_r($members);
        return $members;
    }

    public static function getAllFieldsOfMember(PDO $pdo, string $pseudo, string $password): array {
        $request_str = "SELECT * FROM MEMBER WHERE PSEUDOMEM = ? AND MDPMEM = ?";
        $request = $pdo->prepare($request_str);
        $request->execute(array($pseudo, $password));

        $arr = array();
        if ($request->rowCount() != 1) {
            return $arr;
        }
        $r = $request->fetch();
        $arr['IDMEM'] = $r['IDMEM'];
        $arr['FIRSTNAMEMEM'] = $r['FIRSTNAMEMEM'];
        $arr['LASTNAMEMEM'] = $r['LASTNAMEMEM'];
        $arr['MAILMEM'] = $r['MAILMEM'];
        $arr['PSEUDOMEM'] = $r['PSEUDOMEM'];
        return $arr;
    }

    public static function getAllFieldsOfMemberWithIdentifiant(PDO $pdo, int $id): array {
        $request_str = "SELECT * FROM MEMBER WHERE IDMEM = ?";
        $request = $pdo->prepare($request_str);
        $request->execute(array($id));

        $arr = array();
        if ($request->rowCount() != 1) {
            return $arr;
        }
        $r = $request->fetch();
        $arr['IDMEM'] = $r['IDMEM'];
        $arr['FIRSTNAMEMEM'] = $r['FIRSTNAMEMEM'];
        $arr['LASTNAMEMEM'] = $r['LASTNAMEMEM'];
        $arr['MAILMEM'] = $r['MAILMEM'];
        $arr['PSEUDOMEM'] = $r['PSEUDOMEM'];
        return $arr;
    }

    /**
     * Retourne les informations liées au membre dont le pseudo vaut pseudo et que le mot de passe vaut password
     */
    public static function isExistMemberWithAllInformation(PDO $pdo, array $member): bool {
        $request_str = "SELECT * FROM MEMBER WHERE FIRSTNAMEMEM = ? AND LASTNAMEMEM = ? AND MAILMEM = ? AND PSEUDOMEM = ? AND MDPMEM = ?";
        $request = $pdo->prepare($request_str);
        $request->execute($member);

        return $request->rowCount() == 1;
    }

    /**
     * Retourne les informations liées au membre dont le pseudo vaut pseudo et que le mot de passe vaut password
     */
    public static function isExistMemberWithPseudoAndPass(PDO $pdo, array $member): bool {
        $request_str = "SELECT * FROM MEMBER WHERE PSEUDOMEM = ? AND MDPMEM = ?";
        $request = $pdo->prepare($request_str);
        $request->execute($member);

        return $request->rowCount() == 1;
    }

    /**
     * Retourne les informations liées au membre dont le pseudo vaut pseudo et que le mot de passe vaut password
     */
    public static function isExistPseudo(PDO $pdo, string $pseudo): bool {
        $request_str = "SELECT * FROM MEMBER WHERE PSEUDOMEM = ? ";
        $request = $pdo->prepare($request_str);
        
        $request->execute(array($pseudo));
        return $request->rowCount() == 1;
    }

    public static function isExistEmail(PDO $pdo, string $email): bool {
        $request_str = "SELECT * FROM MEMBER WHERE MAILMEM = ? ";
        $request = $pdo->prepare($request_str);
        
        $request->execute(array($email));
        return $request->rowCount() == 1;
    }

    /**
     * Insère un nouveau membre dans la base de données ainsi que ses paramètres par défaut
     */
    public static function insertMember(PDO $pdo, array $member): void {
        $request_str = "INSERT INTO MEMBER(FIRSTNAMEMEM, LASTNAMEMEM, MAILMEM, PSEUDOMEM, MDPMEM) VALUES (?,?,?,?,?)";
        $request = $pdo->prepare($request_str);
        $request->execute($member);
        Member::insertParameter($pdo, Member::getOneIdentifiantOfMember($pdo, $member));
    }

    public static function insertParameter(PDO $pdo, int $identifiant): void {
        $request_str = "INSERT INTO PARAMETER(MEMIDPARAM) VALUES (?)";
        $request = $pdo->prepare($request_str);
        $request->execute(array($identifiant));
    }

    public static function getOneIdentifiantOfMember(PDO $pdo, array $member): int {
        $request_str = "SELECT * FROM MEMBER WHERE FIRSTNAMEMEM = ? AND LASTNAMEMEM = ? AND MAILMEM = ? AND PSEUDOMEM = ? AND MDPMEM = ?";
        $request = $pdo->prepare($request_str);
        $request->execute($member);

        if ($request->rowCount() == 1) {
            return intval($request->fetch()['IDMEM']);
        }
        return -1;
    }

    public static function isExistOnlyMemberWithOnlyIdentifier(PDO $pdo, int $id): bool {
        $request_str = "SELECT * FROM MEMBER WHERE IDMEM = ?";
        $request = $pdo->prepare($request_str);
        $request->execute(array($id));

        return $request->rowCount() == 1;
    }
    
    public static function removeParameter(PDO $pdo, int $id) {
        $request_str = "DELETE FROM PARAMETER WHERE MEMIDPARAM = ?";
        $request = $pdo->prepare($request_str);
        $request->execute(array($id));
    }

    public static function removeMember(PDO $pdo, int $id) {
        Member::removeParameter($pdo, $id);
        $request_str = "DELETE FROM MEMBER WHERE IDMEM = ?";
        $request = $pdo->prepare($request_str);
        $request->execute(array($id));
    }

    public static function getAllParameterOfMember(PDO $pdo, int $member): array {
        $request_str = "SELECT * FROM PARAMETER WHERE MEMIDPARAM = ?";
        $request = $pdo->prepare($request_str);
        $request->execute(array($member));

        $arr = array();
        if ($request->rowCount() != 1) {
            return $arr;
        }
        $r = $request->fetch();
        // $arr['ISDARKMODEPARAM'] = boolval($r['ISDARKMODEPARAM']);
        $arr['COLORPARAM'] = $r['COLORPARAM'];
        // $arr['NBCONCEPTDISPLAYPARAM'] = intval($r['NBCONCEPTDISPLAYPARAM']);
        return $arr;
    }

    public static function updateMember(PDO $pdo, int $member, array $updating): void {
        for ($i = 0; $i < count($updating); $i++) {
            $key = $updating[$i][0];
            $value = $updating[$i][1];
            $request_str = "UPDATE MEMBER SET " . $key . " = ? WHERE IDMEM = ?";
            // echo "UPDATE MEMBER SET " . $key . " = ". $value ." WHERE IDMEM = " . $member;
            // echo "<br>";
            $request = $pdo->prepare($request_str);
            $request->execute(array($value, $member));
        }
    }

    public static function updateParameterOfMember(PDO $pdo, int $member, array $updating): void {
        for ($i = 0; $i < count($updating); $i++) {
            $key = $updating[$i][0];
            $value = $updating[$i][1];
            $request_str = "UPDATE PARAMETER SET " . $key . " = ? WHERE MEMIDPARAM = ?";
            $request = $pdo->prepare($request_str);
            $request->execute(array($value, $member));
        }
    }

    public static function getAleaPassword(): string {
        $letters = "abcdefghijklmnopqrstuvwxyz";
        $constraints = array(
            "ABCDEFGHIJKLMNOPQRSTUVWXYZ",
            "0123456789",
             "-@_\."
        );

        $pass = substr(str_shuffle($letters), 0, PASS_LEN_MAX - 3);

        foreach ($constraints as $value) {
            $i_letter = random_int(0, strlen($value) - 1);
            $i_pass = random_int(0, strlen($pass) - 1);
            $pass = substr($pass, 0, $i_pass) . $value[$i_letter] . substr($pass, $i_pass);
        }

        return $pass;
    }
}
?>
