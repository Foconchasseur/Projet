<?php

namespace App\Application;

/**
 * All error codes
 */
abstract class ErrorCodes {
    public const USERNAME_OR_PASSWORD_WRONG = "1";
    public const USERNAME_ALREADY_EXISTS = "2";
    public const WRONG_PASSWORD_REPASSWORD = "3";
    public const PASSWORD_TOO_SHORT = "4";
    public const ALL_FIELDS_ARE_REQUIRED = "5";
    public const CONNECTION_REQUIRED = "6";
    public const HQ_DEFINED_ERROR = "7";
    public const SITE_NOT_FOUND = "8";
    public const STUDENT_NOT_FOUND = "9";
    public const STUDENT_ON_CONTRACT = "10";

    public const ALL_REQUIRED_FIELDS_NEED_TO_BE_FILL = "11";

    public const CONTRACT_ALREADY_EXIST = "12";

    public static function getMessage(string $errorCode = ""): string {
        return match ($errorCode) {
            self::USERNAME_OR_PASSWORD_WRONG => "Nom d'utilisateur ou mot de passe incorrect",
            self::USERNAME_ALREADY_EXISTS => "Le nom d'utilisateur existe déjà",
            self::WRONG_PASSWORD_REPASSWORD => "Les mots de passes ne sont pas les mêmes",
            self::PASSWORD_TOO_SHORT => "Le mot de passe saisie est trop court",
            self::ALL_FIELDS_ARE_REQUIRED => "Tout les champs sont requis",
            self::CONNECTION_REQUIRED => "Vous devez être connecté pour accéder à cette page",
            self::HQ_DEFINED_ERROR => "Vous ne pouvez supprimer ce site car il est utlisé en tant que siège par une entreprise",
            self::SITE_NOT_FOUND => "Le site n'a pas été trouvé",
            self::STUDENT_NOT_FOUND => "L'étudiant n'a pas été trouvé",
            self::STUDENT_ON_CONTRACT => "Vous ne pouvez pas supprimer cet étudiant car il possède déja un contrat",
            self::ALL_REQUIRED_FIELDS_NEED_TO_BE_FILL =>"Vous devez renseigner tout les champs indiqués obligatoires",
            self::CONTRACT_ALREADY_EXIST => "Le contrat que vous souhaitez créer existe déjà",
            default => "Une erreur s'est produite de notre côté, veuillez réessayer",
        };
    }
}
