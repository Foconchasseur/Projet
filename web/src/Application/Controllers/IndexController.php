<?php
namespace App\Application\Controllers;

use App\Application\Models\Entreprise;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * IndexController
 */
class IndexController extends Controller {
    /**
     * Home page
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return ResponseInterface
     */
    public function index(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
        return $this->container->get('view')->render($response, 'home.twig', [
            'name' => 'Meow meow',
            'enterprises' => Entreprise::getAll(),
        ]);
    }

    public function aboutus(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
        return $this->container->get('view')->render($response, 'aboutus.twig', []);
    }
}