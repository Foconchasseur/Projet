<?php
namespace App\Application;

use Exception;

/**
 * Autoloader because we are too lazy to write require_once or include_once
 */
abstract class Autoloader {
    private const CONTROLLER_PATH = __DIR__ . '/Controllers/';
    private const MODELS_PATH = __DIR__ . '/Models/';

    /**
     * Autoload controllers/models when they are called
     * @return void
     */
    public static function autoload(): void
    {
        /*
         * Forced to surround it by try catch because he won't listen to throw=false
         * What a stubborned function
         */
        try {
            spl_autoload_register(function ($class) {
                if (file_exists(self::MODELS_PATH . $class . '.php')) {
                    require self::MODELS_PATH . $class . '.php';
                } else if (file_exists(self::CONTROLLER_PATH . $class . '.php')) {
                    require self::CONTROLLER_PATH . $class . '.php';
                }
            }, true, true);
        } catch (Exception) {
            // Go check my gitlab it is very cool (https://gitlab.com/skewram)
        }
    }
}
