<?php

class Database {
    
    /**
     * Démarre la session pour le membre dont l'identifiant vaut $i.
     */
    public static function startSession(): PDO {
        if (session_start()) {
            $username = "projet";
            $password = "tejorp";
            $pdo = new PDO("mysql:host=localhost;dbname=projet", $username, $password);
            return $pdo;
        }
        return NULL;
    }

    /**
     * Met fin à la session de l'utilisateur dont l'identifiant vaut $i.
     */
    public static function endSession(): void {
        session_destroy();
    }
}

?>
