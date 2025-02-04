<?php

declare(strict_types=1);

namespace App\Application\Middleware;

use App\Application\Flash;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class SessionMiddleware implements Middleware
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
        Flash::clean();

        return $handler->handle($request);
    }
}
