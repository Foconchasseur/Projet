<?php

namespace App\Application\Controllers;

use App\Application\Database;
use App\Application\ErrorCodes;
use App\Application\Flash;
use App\Application\Models\Site;
use Psr\Container\ContainerExceptionInterface;
use App\Application\Settings\SettingsInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Exception;
use DI\Container;

/**
 * Controller base class
 */
class SitesController extends Controller {

    /**
     * @throws Exception
     */
    public function index(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
        return $this->getPage($response);
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return ResponseInterface
     * @throws Exception
     */
    public function update(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
        $data = $request->getParsedBody();
        $data = self::cleanInputs($data);
        $site = Site::get([
            'pays' => $data["oldPays"],
            'ville' => $data["oldVille"],
            'adresse' => $data["oldAdresse"]
        ]);

        if ($site != null){
            $site->setOuvert(!empty($data["etat"]));
            $site->setVille($data["ville"]);
            $site->setPays($data["pays"]);
            $site->setAdresse($data["adresse"]);
            if($site->save()){
                Flash::bang("success","Site modifié avec succès");
            }else{
                Flash::bang("error",ErrorCodes::getMessage());
            }
        } else{
            Flash::bang("error",ErrorCodes::getMessage());
        }

        $response = $response->withStatus(302);
        return $response->withHeader("Location", "/sites");
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return ResponseInterface
     * @throws Exception
     */
    public function add(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
        $data = $request->getParsedBody();
        $data = self::cleanInputs($data);
        $site = new Site(!empty($data["etat"]),$data["pays"],$data["ville"],$data["adresse"]);
        if($site->save()){
            Flash::bang("success","Site ajouté avec succès");
        }else{
            Flash::bang("error",ErrorCodes::getMessage());
        }
        $response = $response->withStatus(302);
        return $response->withHeader("Location", "/sites");
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return ResponseInterface
     * @throws Exception
     */
    public function delete(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
        $data = $request->getQueryParams();
        $data = self::cleanInputs($data);
        $site = Site::get([
            'pays'  =>  $data["pays"],
            'ville' =>  $data["ville"],
            'adresse' => $data["adresse"]
        ]);

        if ($site) {
            if($site->delete()){
                Flash::bang("success", "Le site a été supprimé avec succès");
            }else{
                Flash::bang("error", ErrorCodes::getMessage(ErrorCodes::HQ_DEFINED_ERROR));
            }
        } else {
            Flash::bang("error", ErrorCodes::getMessage(ErrorCodes::SITE_NOT_FOUND));
        }

        $response = $response->withStatus(302);
        return $response->withHeader("Location", "/sites");
    }

    private function getPage(ResponseInterface $response){
        $data = Site::getAll(35,"pays",1);
        return $this->container->get('view')->render(
            $response, 'sites/sites.twig',
            [
                'sites' => $data,
                'countries' => $this->container->get(SettingsInterface::class)->get('countries')
            ]
        );
    }

    /**
     * @throws Exception
     */
    public function villes(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
        try {
            if (empty($request->getQueryParams()['pays'])) throw new Exception("Le pays doit être renseigné");
            $pays = $request->getQueryParams()['pays'];

            $db = Database::getInstance();

            $pays = $db->quote($pays);
            $data = $db->query("select distinct ville from site where pays=$pays order by ville")
                ->fetchAll();
            $data = array_map(function ($element) {
                return $element[0];
            }, $data);

            if (!$data) {
                $response->getBody()->write(json_encode(['error' => true, 'message' => "Une erreur est survenue"]));
            } else {
                $response->getBody()->write(json_encode(['error' => false, 'villes' => $data]));
            }
        } catch (Exception $e) {
            $response->getBody()->write(json_encode(['error' => true, 'message' => $e->getMessage()]));
        }
        return $response;
    }

    /**
     * @throws Exception
     */
    public function adresses(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
        try {
            if (empty($request->getQueryParams()['pays'])) throw new Exception("Le pays doit être renseigné");
            if (empty($request->getQueryParams()['ville'])) throw new Exception("La ville doit être renseignée");
            $pays = $request->getQueryParams()['pays'];
            $ville = $request->getQueryParams()['ville'];

            $db = Database::getInstance();

            $pays = $db->quote($pays);
            $ville = $db->quote($ville);
            $data = $db->query("select distinct adresse from site where pays=$pays and ville=$ville order by adresse")
                ->fetchAll();
            $data = array_map(function ($element) {
                return $element[0];
            }, $data);

            if (!$data) throw new Exception("Une erreur est survenue");

            $response->getBody()->write(json_encode(['error' => false, 'adresses' => $data]));
        } catch (Exception $e) {
            $response->getBody()->write(json_encode(['error' => true, 'message' => $e->getMessage()]));
        }
        return $response;
    }
}
