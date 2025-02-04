<?php

namespace App\Application\Controllers;

use App\Application\ErrorCodes;
use App\Application\Flash;
use App\Application\Models\ContratSite;
use App\Application\Models\ContratVacataire;
use App\Application\Models\Employe;
use App\Application\Models\Entreprise;
use App\Application\Models\Site;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ContratVacataireController extends Controller
{
    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return ResponseInterface
     * This function allow us to get to the main contract vacation page
     */
    public function index(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
        return $this->getPage($response);
    }

    /**
     * @param ResponseInterface $response
     * @return mixed
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * This function return the main page filled with data
     */
    private function getPage(ResponseInterface $response){
        $data = ContratVacataire::getAll();
        return $this->container->get('view')->render(
            $response, 'contratVacataire/contratVacataire.twig',
            [
                'contratVacataires' => $data,
                'sites' => Site::getAll(),
                'entreprises' => Entreprise::getAll(),
                'vacataires' => Employe::getAll()
            ]
        );
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return ResponseInterface
     * @throws \Exception
     * This function delete contract of vacation based of the values given from the front
     */
    public function delete(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
        $data = $request->getQueryParams();

        $contratVacataire = ContratVacataire::get([
            'date_debut'  =>  htmlspecialchars($data["date"] ?? ""),
            'nom_vacataire' =>  htmlspecialchars($data["nom"] ?? ""),
            'prenom_vacataire' => htmlspecialchars($data["prenom"] ?? "")
        ]);
        if ($contratVacataire) {
            if($contratVacataire->delete()){
                Flash::bang("success", "Le contrat de vacation a été supprimé avec succès");
            }else{
                Flash::bang("error", ErrorCodes::getMessage(ErrorCodes::getMessage()));
            }
        } else {
            Flash::bang("error", ErrorCodes::getMessage(ErrorCodes::getMessage()));
        }

        $response = $response->withStatus(302);
        return $response->withHeader("Location", "/contratVacataire");
    }


    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return ResponseInterface
     * @throws \Exception
     * This function update contract of vacation based of the values given from the front
     */
    public function update(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
        $data = $request->getParsedBody();
        $contrat = ContratVacataire::get([
            'nom_vacataire'=>$data["oldNom"],
            'prenom_vacataire'=>$data["oldPrenom"],
            'date_debut'=>$data["oldDate"]
        ]);
        if ($contrat != null){
            $contrat->setNote($data["note"] == "" ? : $data["note"]);
            $vacataire = explode(";",$data["vacataire"]);
            $contrat->setVacataire(Employe::get([
                'prenom' => htmlspecialchars($vacataire[1] ?? ""),
                'nom' => htmlspecialchars($vacataire[0] ?? "")
                ]));
            $site = explode(";",$data["site"]);
            $contrat->setSite(new Site(
                $contrat->getSite()->isOuvert(),
                $site[0],
                $site[1],
                $site[2]
            ));
            $contrat->setDateFinAnticipe($data["dateFinAnt"]);
            $contrat->setDateFin($data["dateFin"]);
            $contrat->setDateDebut($data["dateDeb"]);
            $contrat->setEntreprise(Entreprise::get(['nom' => $data["Entreprise"]]));
            if($contrat->save()){
                Flash::bang("success","Contrat modifié avec succès");
            }else{
                Flash::bang("error",ErrorCodes::getMessage());
            }
        } else{
            Flash::bang("error",ErrorCodes::getMessage());
        }
        $response = $response->withStatus(302);
        return $response->withHeader("Location", "/contratVacataire");
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return ResponseInterface
     * @throws \Exception
     * This function create contract of vacation based of the values given from the front
     */
    public function add(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
        $data = $request->getParsedBody();
        if ($data["vacataire"] & $data["Entreprise"] & $data["dateDeb"] & $data["dateFin"] & $data["site"]){
            $site = explode(";",$data["site"]);
            $vacataire = explode(";",$data["vacataire"]);

            if (ContratVacataire::get([
                'nom_vacataire'=>$vacataire[0],
                'prenom_vacataire'=>$vacataire[1],
                'date_debut'=>$data["dateDeb"]
            ])){
                Flash::bang("error",ErrorCodes::getMessage(ErrorCodes::CONTRACT_ALREADY_EXIST));
            }else{
                $contrat = new ContratVacataire(
                    $data["dateDeb"],
                    $data["dateFin"],
                    Entreprise::get(['nom' => $data["Entreprise"]]),
                    Site::get([
                        'pays'      => $site[0],
                        'ville'     => $site[1],
                        'adresse'   => $site[2]
                    ]),
                    $data["dateFinAnt"],
                    $data["note"] === "" ? -1 : $data["note"],
                    Employe::get([
                        'nom' => htmlspecialchars($vacataire[0] ?? ""),
                        'prenom' => htmlspecialchars($vacataire[1] ?? "")
                    ])
                );
                if($contrat->save()){
                    Flash::bang("success","Contrat ajouté avec succès");
                }else{
                    Flash::bang("error","ma teub");
                }
            }
        }else{
            Flash::bang("error",ErrorCodes::getMessage(ErrorCodes::ALL_REQUIRED_FIELDS_NEED_TO_BE_FILL));
        }
        $response = $response->withStatus(302);
        return $response->withHeader("Location", "/contratVacataire");
    }
}