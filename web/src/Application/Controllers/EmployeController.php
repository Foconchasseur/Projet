<?php

namespace App\Application\Controllers;

use App\Application\Database;
use App\Application\ErrorCodes;
use App\Application\Flash;
use App\Application\Models\Donation;
use App\Application\Models\Employe;
use App\Application\Models\Entreprise;
use App\Application\Models\Etudiant;
use App\Application\Models\Site;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * EmployeController
 */
class EmployeController extends Controller
{
    /**
     * Home page
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return ResponseInterface
     */
    public function index(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        return $this->container->get('view')->render($response, 'employe/employe.twig', [
            'employes' => Employe::getAll(),
        ]);
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return ResponseInterface
     */
    public function delete(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $args = $request->getQueryParams();
        $args = self::cleanInputs($args);
        $employe = Employe::get([
            'nom' => $args['nom'],
            'prenom' => $args['prenom']
        ]);

        if ($employe) {
            $return = $employe->delete();
            if (!$return) {
                Flash::bang("error", ErrorCodes::getMessage("L'employé possède un contrat"));
            } else {
                Flash::bang("success", "L'employé à bien été supprimé");
            }
        } else {
            Flash::bang("error", ErrorCodes::getMessage("Employé introuvable"));
        }

        $response = $response->withStatus(302);
        return $response->withHeader('Location', '/employe');
    }

    public function update(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $data = $request->getParsedBody();
        $data = self::cleanInputs($data);

        $employe = Employe::get([
            'nom' => $data["oldNom"],
            'prenom' => $data["oldPrenom"]
        ]);

        $employe->setPrenom($data["nom"]);
        $employe->setNom($data["prenom"]);
        $employe->save();

        $response = $response->withStatus(302);
        return $response->withHeader('Location', '/employe');
    }

    public function add(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        // TODO Voir si on se retrouve pas dans le même cas qu'Ewen avec l'update/insert de sites
        $data = $request->getParsedBody();
        $data = self::cleanInputs($data);
        $prenom = $data['prenom'];
        $nom = $data['nom'];

        if ($employe = Employe::get(['nom' => $nom, 'prenom' => $prenom])) {
            // L'employe existe deja
            $employe->setNom($nom);
            $employe->setPrenom($prenom);
            if ($employe->save()) {
                Flash::bang("success", "L'employé a été mis à jour");
            } else {
                Flash::bang("error", "L'employé n'a pas pu être mis à jour");
            }
        } else {
            //l'étudiant existe pas
            $employe = new Employe($nom, $prenom);
            if ($employe->save()) {
                Flash::bang("success", "L'employé a pu être créé");
            } else {
                Flash::bang("error", "L'employé n'a pas pu être créé");
            }
        }
        
        $response = $response->withStatus(302);
        return $response->withHeader('Location', '/employe');
    }
}
