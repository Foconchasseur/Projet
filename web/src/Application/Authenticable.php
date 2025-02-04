<?php

namespace App\Application;

use App\Application\Models\Utilisateur;

trait Authenticable {

    /**
     * Authenticate a user
     * @param string $username
     * @param string $password
     * @return bool true on success false on failure
     * @throws \Exception
     */
    public static function login(string $username, string $password): bool {
        $pdo = Database::getInstance();
        $req = $pdo->prepare("SELECT * FROM ".static::$table." WHERE ".static::$primary_key."=?");
        $req->execute([$username]);
        $res = $req->fetch();
        if ($req->rowCount() > 0 && password_verify($password, $res['password'])) {
            $_SESSION['user'] = static::get([static::$primary_key => $username]);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check if a user exists
     * @param string $username
     * @return bool
     * @throws \Exception
     */
    public static function checkIfExists(string $username): bool {
        $pdo = Database::getInstance();
        $req = $pdo->prepare("SELECT * FROM ".static::$table." WHERE ".static::$primary_key."=?");
        $req->execute([$username]);
        return $req->rowCount() > 0;
    }

    /**
     * Disconnect a user
     * @return bool
     */
    public static function logout(): bool {
        $_SESSION["user"] = null;
        unset($_SESSION["user"]);
        return true;
    }
}