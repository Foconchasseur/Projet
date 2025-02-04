<?php

namespace App\Application\Controllers;

use App\Application\Database;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class VueController extends Controller
{

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function entpCourrier(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $data = Database::getInstance()
            ->query("select * from VueEntpCourrier order by nom")
            ->fetchAll();
        return $this->container->get('view')->render($response, 'vue/afficheVue.twig', [
            'entreprises' => $data, 
            'view' => 'vue/entpCourrier.twig',
        ]);
    }


    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function entpBlocked(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $data = Database::getInstance()
            ->query("select * from VueEntpBlocked order by nom")
            ->fetchAll();
        return $this->container->get('view')->render($response, 'vue/afficheVue.twig', ['entreprises' => $data, 'view' => 'vue/entpBlocked.twig']);
    }


    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function vacMds(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $data = Database::getInstance()
            ->query("select * from vuevacmds order by nom, prenom")
            ->fetchAll();
        return $this->container->get('view')->render($response, 'vue/afficheVue.twig', ['vacMds' => $data,'view' => 'vue/vacMds.twig']);
    }


    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function vacMdsCurrent(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $data = Database::getInstance()
            ->query("select * from vuevacmdsCurrent order by nom, prenom")
            ->fetchAll();
        return $this->container->get('view')->render($response, 'vue/afficheVue.twig', ['vacMds' => $data, 'view' => 'vue/vacMdsCurrent.twig']);
    }


    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function entpNbByContrat(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $data = Database::getInstance()
            ->query("select * from VueEntpNbByContrat order by nb_total desc")
            ->fetchAll();
        return $this->container->get('view')->render($response, 'vue/afficheVue.twig', ['entreprises' => $data, 'view' => 'vue/entpNbByContrat.twig']);
    }


    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function laboContrat(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $data = Database::getInstance()
            ->query("select * from vuelabocontrat order by laboratoire, date_debut")
            ->fetchAll();

        $data_processed = [];
        foreach ($data as $contrat) {
            $contrat['date_debut'] = date('d/m/Y', strtotime($contrat['date_debut']));
            $contrat['date_fin'] = date('d/m/Y', strtotime($contrat['date_fin']));
            if ($contrat['date_fin_anticipe'] != null) $contrat['date_fin_anticipe'] = date('d/m/Y', strtotime($contrat['date_anticipe']));
            $data_processed[$contrat['laboratoire']][] = $contrat;
        }

        return $this->container->get('view')->render($response, 'vue/afficheVue.twig', ['laboratoires' => $data_processed, 'view' => 'vue/laboContrat.twig']);
    }


    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function mds(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $data = Database::getInstance()
            ->query("select * from vueMds order by nom_maitre, prenom_maitre")
            ->fetchAll();
        return $this->container->get('view')->render($response, 'vue/afficheVue.twig', ['maitres' => $data, 'view' => 'vue/mds.twig']);
    }


    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function vacataire(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $data = Database::getInstance()
            ->query("select * from vuevacataire order by nom_vacataire, prenom_vacataire")
            ->fetchAll();
        return $this->container->get('view')->render($response, 'vue/afficheVue.twig', ['vacataires' => $data, 'view' => 'vue/vacataire.twig']);
    }


    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function alternant(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $data = Database::getInstance()
            ->query("select * from vueAlternant order by no_etu, nationalite")
            ->fetchAll();
        return $this->container->get('view')->render($response, 'vue/afficheVue.twig', ['alternants' => $data, 'view' => 'vue/alternant.twig']);
    }


    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function stagiaire(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $data = Database::getInstance()
            ->query("select * from vueStagiaire order by no_etu, nationalite")
            ->fetchAll();
        return $this->container->get('view')->render($response, 'vue/afficheVue.twig', ['stagiaires' => $data, 'view' => 'vue/stagiaire.twig']);
    }


    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function etudiant(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $data = Database::getInstance()
            ->query("select * from vueEtudiant order by no_etu, nationalite")
            ->fetchAll();
        return $this->container->get('view')->render($response, 'vue/afficheVue.twig', ['etudiants' => $data, 'view' => 'vue/etudiant.twig']);
    }


    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function conflict(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $data = Database::getInstance()
            ->query("select * from vueConflict")
            ->fetchAll();
        return $this->container->get('view')->render($response, 'vue/afficheVue.twig', ['contrats' => $data, 'view' => 'vue/conflict.twig']);
    }


    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function yearStatEntps(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $data = Database::getInstance()
            ->query("select * from vueyearstatentps order by entreprise, annee")
            ->fetchAll();
        return $this->container->get('view')->render($response, 'vue/afficheVue.twig', ['notes' => $data, 'view' => 'vue/yearStatEntps.twig']);
    }


    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function conflitEtudiant(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $data = Database::getInstance()
            ->query("select * from vueConflitEtudiant order by no_etu, annee")
            ->fetchAll();
        return $this->container->get('view')->render($response, 'vue/afficheVue.twig', ['contrats' => $data, 'view' => 'vue/conflitEtudiant.twig']);
    }


    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function conflitEtudiant2(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $data = Database::getInstance()
            ->query("select * from vueConflitEtudiant2")
            ->fetchAll();
        return $this->container->get('view')->render($response, 'vue/afficheVue.twig', ['conflits' => $data, 'view' => 'vue/conflitEtudiant2.twig']);
    }


    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function mdsNote(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $data = Database::getInstance()
            ->query("select * from vueMdsNote")
            ->fetchAll();
        return $this->container->get('view')->render($response, 'vue/afficheVue.twig', ['notes' => $data, 'view' => 'vue/mdsNote.twig']);
    }
}
