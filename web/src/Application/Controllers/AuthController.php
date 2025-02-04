<?php

namespace App\Application\Controllers;

use App\Application\ErrorCodes;
use App\Application\Flash;
use App\Application\Models\Utilisateur;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class AuthController extends Controller
{
    /**
     * Login page
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return ResponseInterface
     */
    public function login(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
        return $this->container->get('view')->render($response, 'login.twig');
    }

    /**
     * Post login page
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return ResponseInterface
     * @throws \Exception
     */
    public function postLogin(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
        $data = $request->getParsedBody();
        $data = self::cleanInputs($data);

        $response = $response->withStatus(302);

        if (empty($data['username']) || empty($data['password'])) {
            Flash::bang('error', ErrorCodes::getMessage(ErrorCodes::ALL_FIELDS_ARE_REQUIRED));
        } else if (Utilisateur::login($data['username'], $data['password'])) {
            return $response->withHeader('Location', '/');
        } else {
            Flash::bang('error', ErrorCodes::getMessage(ErrorCodes::USERNAME_OR_PASSWORD_WRONG));
            return $response->withHeader('Location', '/login');
        }
        return $response->withHeader('Location', '/login');
    }

    /**
     * Register page
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return ResponseInterface
     */
    public function register(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
        return $this->container->get('view')->render($response, 'register.twig');
    }

    /**
     * Post register page
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return ResponseInterface
     * @throws \Exception
     */
    public function postRegister(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
        $data = $request->getParsedBody();
        $data = self::cleanInputs($data);

        $response = $response->withStatus(302);

        if (empty($data['surname']) || empty($data['name']) || empty($data['username']) || empty($data['password']) || empty($data['repassword'])) {
            Flash::bang('error', ErrorCodes::getMessage(ErrorCodes::ALL_FIELDS_ARE_REQUIRED));
            return $response->withHeader('Location', '/register');
        }

        $surname = $data['surname'];
        $name = $data['name'];
        $login = $data['username'];
        $password = $data['password'];
        $repassword = $data['repassword'];

        if ($password !== $repassword) {
            Flash::bang('error', ErrorCodes::getMessage(ErrorCodes::WRONG_PASSWORD_REPASSWORD));
            return $response->withHeader('Location', '/register');
        }

        if (Utilisateur::checkIfExists($login)) {
            Flash::bang('error', ErrorCodes::getMessage(ErrorCodes::USERNAME_ALREADY_EXISTS));
            return $response->withHeader('Location', '/register');
        }

        $user = new Utilisateur($name, $surname, $login, password_hash($password, PASSWORD_BCRYPT));
        if ($user->save()) {
            Flash::bang('success', 'Compte créé avec succès !');
            return $response->withHeader('Location', '/login');
        } else {
            Flash::bang('error', ErrorCodes::getMessage("-1"));
            return $response->withHeader('Location', '/register');
        }
    }

    public function logout(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
        Utilisateur::logout();
        Flash::bang("success", "Vous avez été déconnecté avec succès");
        $response = $response->withStatus(301);
        return $response->withHeader("Location", "/login");
    }
}