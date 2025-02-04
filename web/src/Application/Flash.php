<?php

namespace App\Application;

/**
 * Utility class for creation of temporary session through a request
 */
abstract class Flash
{
    const FLASH_NEW_ATTRIBUTE = "flash_new";
    const FLASH_OLD_ATTRIBUTE = "flash_old";

    /**
     * Initialize the $_SESSION object to handle Flash messages
     * @return void
     */
    public static function initialize(): void
    {
        $_SESSION[static::FLASH_NEW_ATTRIBUTE] = [];
        $_SESSION[static::FLASH_OLD_ATTRIBUTE] = [];
    }

    /**
     * Put a key,value in the flash session
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public static function bang(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
        $_SESSION[static::FLASH_NEW_ATTRIBUTE][] = $key;
        $_SESSION[static::FLASH_OLD_ATTRIBUTE] = array_diff($_SESSION[static::FLASH_OLD_ATTRIBUTE], [$key]);
    }

    /**
     * Called by a middleware, clean $_SESSION from old flash messages
     * @return void
     */
    public static function clean(): void
    {
        $_SESSION[static::FLASH_OLD_ATTRIBUTE] = $_SESSION[static::FLASH_NEW_ATTRIBUTE];
        foreach ($_SESSION[static::FLASH_OLD_ATTRIBUTE] as $key) {
            $_SESSION[$key] = null;
            unset($_SESSION[$key]);
        }
        $_SESSION[static::FLASH_NEW_ATTRIBUTE] = [];
    }
}