<?php

namespace App\Application\Controllers;

use App\Application\Database;
use App\Application\Models\ContratLabo;
use App\Application\Models\Laboratoire;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use function PHPUnit\Framework\isNull;

class LaboratoireController extends Controller {

    /**
     * @return ResponseInterface la page de la liste des labos
     * @throws NotFoundExceptionInterface
     * @throws Exception
     * @throws ContainerExceptionInterface
     */
    public function liste(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
        $data = Laboratoire::getAll(-1, 'nom');
        return $this->container->get('view')->render($response, 'laboratoire/liste.twig', ['laboratoires' => $data]);
    }

    /**
     * Ajoute en base s'il n'est pas déja présent
     * renvois un json au format suivant :
     * [error => boolean, message => string?]
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return ResponseInterface
     */
    public function add(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
        try {
            $data = $request->getQueryParams();
            $data = self::cleanInputs($data);
            if (empty($data['nom'])) throw new Exception('le nom doit être renseigné');

            $nom = $data['nom'];
            if (Laboratoire::get(['nom' => $nom])) throw new Exception('il existe déja un laboratoire avec ce nom.');
            $laboratoire = new Laboratoire($nom);
            $laboratoire->save();
            $response->getBody()->write(json_encode(['error' => false]));
        } catch (Exception $e) {
            $response->getBody()->write(json_encode(['error' => true, 'message' => $e->getMessage()]));
        }
        return $response;
    }

    public function delete(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
        try {
            $data = $request->getQueryParams();
            $data = self::cleanInputs($data);
            if (empty($data['nom'])) throw new Exception('le nom doit être renseigné');

            $nom = $data['nom'];

            if (ContratLabo::get(['laboratoire' => $nom])) throw new Exception('Le laboratoire a des contrats liés.');

            $laboratoire = new Laboratoire($nom);
            $laboratoire->delete();
            $response->getBody()->write(json_encode(['error' => false]));
        } catch (Exception $e) {
            $response->getBody()->write(json_encode(['error' => true, 'message' => $e->getMessage()]));
        }
        return $response;
    }

    public function update(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
        try {
            $data = $request->getQueryParams();
            $data = self::cleanInputs($data);
            if (empty($data['old_nom'])) throw new Exception('le nom doit être renseigné');

            $nom = $data['old_nom'];
            $new_nom = $data['new_nom'];

            $laboratoire = new Laboratoire($nom);

            if (!empty($new_nom)) $laboratoire->setNom($new_nom);
            if (Laboratoire::get(['nom' => $new_nom])) throw new Exception('il existe déja un laboratoire avec ce nom.');


            if ($laboratoire->save()) {
                $response->getBody()->write(json_encode(['error' => false]));
            } else {
                throw new Exception("La mise à jour du laboratoire a échouée");
            }
        } catch (Exception $e) {
            $response->getBody()->write(json_encode(['error' => true, 'message' => $e->getMessage()]));
        }
        return $response;
    }
}