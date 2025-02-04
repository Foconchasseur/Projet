<?php

declare(strict_types=1);

namespace App\Application\Middleware;

use App\Application\ErrorCodes;
use App\Application\Flash;
use App\Application\Models\Utilisateur;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class AuthenticationMiddleware implements Middleware
{
    /**
     * {@inheritdoc}
     * @throws \Exception
     */
    public function process(Request $request, RequestHandler $handler): Response
    {
        if (!isset($_SESSION[Flash::FLASH_NEW_ATTRIBUTE]) || !isset($_SESSION[Flash::FLASH_OLD_ATTRIBUTE])) {
            Flash::initialize();
        }

        $response = $handler->handle($request);

        if (isset($_SESSION["user"])) {
            $user = $_SESSION["user"];
            if ($user instanceof Utilisateur && Utilisateur::checkIfExists($user->getLogin())) {
                return $response;
            }
        }

        Flash::bang("error", ErrorCodes::getMessage(ErrorCodes::CONNECTION_REQUIRED));
        $response = $response->withStatus(301);
        return $response->withHeader("Location", "/login");
    }
}
