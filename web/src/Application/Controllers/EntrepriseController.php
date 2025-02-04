<?php

namespace App\Application\Controllers;

use App\Application\Database;
use App\Application\Models\Entreprise;
use App\Application\Models\Site;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use function PHPUnit\Framework\isNull;

class EntrepriseController extends Controller {

    /**
     * @return ResponseInterface la page de la liste des labos
     * @throws NotFoundExceptionInterface
     * @throws Exception
     * @throws ContainerExceptionInterface
     */
    public function liste(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
        $data = Entreprise::getAll(-1, 'nom');

        $pays = array_map(
            function ($row) {
                return $row[0];
            },
            Database::getInstance()
                ->query('select distinct pays from site order by pays')
                ->fetchAll()
        );

        return $this->container->get('view')->render($response, 'entreprise/liste.twig', [
            'entreprises' => $data,
            'pays' => $pays
        ]);
    }

    public function delete(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
        try {
            if (
                !isset($request->getQueryParams()['nom']) ||
                empty($request->getQueryParams()['nom'])
            ) throw new Exception('le nom doit être renseigné');
            $entreprise = Entreprise::get(['nom' => $request->getQueryParams()['nom']]);
            if (!$entreprise->delete()) throw new Exception("Une erreur est survenue");
            $response->getBody()->write(json_encode(['error' => false]));
        } catch (Exception $e) {
            $response->getBody()->write(json_encode(['error' => true, 'message' => $e->getMessage()]));
        }
        return $response;
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
            if (empty($request->getParsedBody()['nom'])) throw new Exception('le nom doit être renseigné');
            if (empty($request->getParsedBody()['pays'])) throw new Exception('le pays doit être renseigné');
            if (empty($request->getParsedBody()['ville'])) throw new Exception('la ville du siège doit être renseigné');
            if (empty($request->getParsedBody()['adresse'])) throw new Exception('l\'adresse du siège doit être renseigné');
            if (empty($request->getParsedBody()['nb_employe'])) throw new Exception('le nombre d\'employés doit être renseigné');

            $nom = $request->getParsedBody()['nom'];
            $paysSiege = $request->getParsedBody()['pays'];
            $villeSiege = $request->getParsedBody()['ville'];
            $adresseSiege = $request->getParsedBody()['adresse'];
            $ouvert = ($request->getParsedBody()['ouvert'] ?? "on") == "on";
            $nbEmploye = $request->getParsedBody()['nb_employe'];

            if (Entreprise::get(['nom' => $nom])) throw new Exception('il existe déja une entreprise avec ce nom.');

            $site = Site::get(['pays' => $paysSiege, 'ville' => $villeSiege, 'adresse' => $adresseSiege]);
            if (!$site) throw new Exception('Le siège est introuvable en base de donnée');

            $entreprise = new Entreprise($site, $ouvert, $nom, $nbEmploye);
            if (!$entreprise->save()) throw new Exception("Une erreur est survenue");

            $response->getBody()->write(json_encode(['error' => false]));
        } catch (Exception $e) {
            $response->getBody()->write(json_encode([
                'error' => true,
                'message' => $e->getMessage(),
            ]));
        }
        return $response;
    }

    public function update(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
        try {
            if (empty($request->getParsedBody()['old_nom'])) throw new Exception('le nom doit être renseigné.');

            $old_nom = $request->getParsedBody()['old_nom'];

            $entreprise = Entreprise::get(['nom' => $old_nom]);

            if (isset($request->getParsedBody()['nom'])) {
                $new_nom = $request->getParsedBody()['nom'];
                if (
                    Entreprise::get(['nom' => $new_nom]) &&
                    $entreprise->getNom() != $request->getParsedBody()['nom']
                ) throw new Exception('Il éxiste déja une entreprise qui porte ce nom');

                $entreprise->setNom($request->getParsedBody()['nom']);
            }

            if (
                isset($request->getParsedBody()['pays']) &&
                isset($request->getParsedBody()['ville']) &&
                isset($request->getParsedBody()['adresse'])
            ) {
                $newSiege = Site::get([
                    'pays' => $request->getParsedBody()['pays'],
                    'ville' => $request->getParsedBody()['ville'],
                    'adresse' => $request->getParsedBody()['adresse']
                ]);
                if(!$newSiege) throw new Exception("Site introuvable");

                $entreprise->setSiege($newSiege);
            }

            if (isset($request->getParsedBody()['nb_employe'])) {
                $entreprise->setNbEmploye($request->getParsedBody()['nb_employe']);
            }

            if(isset($request->getParsedBody()['ouvert'])){
                $entreprise->setOuvert($request->getParsedBody()['ouvert']== "true");
            }

            if(!$entreprise->save()) throw new Exception("Une erreur est survenue");
            $response->getBody()->write(json_encode(['error' => false]));
        } catch (Exception $e) {
            $response->getBody()->write(json_encode(['error' => true, 'message' => $e->getMessage()]));
        }
        return $response;
    }
}