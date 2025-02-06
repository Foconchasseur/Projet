<?php

namespace App\Application;

use App\Application\Settings\SettingsInterface;
use DI\Container;
use Exception;

class Database extends \PDO
{
    private static $instance;

    /**
     * init the db
     * @return void
     * @throws Exception
     */
    private function __construct(Container $container)
    {
        $database_settings = $container->get(SettingsInterface::class)->get('database');
        $driver     = $database_settings['driver'];
        $user       = $database_settings['user'];
        $password   = $database_settings['password'];
        $host       = '192.168.44.254'; // Adresse IP du serveur distant
        $name       = $database_settings['dbname'];
        $port       = $database_settings['port'];

        try {
            $dsn = "$driver:host=$host;dbname=$name;port=$port";
            parent::__construct($dsn, $user, $password);
        } catch (PDOException $e) {
            throw new Exception("Error connecting to database: " . $e->getMessage());
        }
    }


    /**
     * @throws Exception
     */
    public static function getInstance(Container $container = null): Database
    {
        if (self::$instance == null && $container !== null) {
            self::$instance = new self($container);
        }
        return self::$instance;
    }
}
