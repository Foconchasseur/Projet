<?php
namespace App\Application\Controllers;

use Psr\Container\ContainerInterface;

/**
 * Controller base class
 */
abstract class Controller {
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    protected static function cleanInputs(array $array) {
        $cleaned = [];

        foreach ($array as $k => $v) {
            $cleaned[$k] = htmlspecialchars($v);
        }

        return $cleaned;
    }
}
