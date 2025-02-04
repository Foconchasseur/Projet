<?php

declare(strict_types=1);

use App\Application\Autoloader;
use App\Application\Controllers\EntrepriseController;
use App\Application\Controllers\AuthController;
use App\Application\Controllers\ContratVacataireController;
use App\Application\Controllers\EtudiantController;
use App\Application\Controllers\IndexController;
use App\Application\Controllers\LaboratoireController;
use App\Application\Controllers\SitesController;
use App\Application\Controllers\VueController;
use App\Application\Controllers\EmployeController;
use App\Application\Middleware\AuthenticationMiddleware;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

/*
 * Because we are lazy we want our Controllers to be loaded on the fly
 */
require __DIR__ . '/../src/Application/Autoloader.php';
Autoloader::autoload();

return function (App $app) {
    $app->get('/', IndexController::class . ':index')->add(new AuthenticationMiddleware());

    $app->get('/login', AuthController::class . ':login');
    $app->get('/register', AuthController::class . ':register');
    $app->post('/login', AuthController::class . ':postLogin');
    $app->post('/register', AuthController::class . ':postRegister');
    $app->get('/logout', AuthController::class . ':logout');
    
    $app->group('/students', function (Group $group) {
        $group->get('', EtudiantController::class . ':index');
        $group->get('/delete', EtudiantController::class . ':delete');
        $group->post('/add', EtudiantController::class . ':add');
        $group->post('/update',  EtudiantController::class . ':edit');
    })->add(new AuthenticationMiddleware());

    $app->group('/sites', function (Group $group) {
        $group->get('', SitesController::class.':index');
        $group->get('/villes', SitesController::class . ':villes');
        $group->get('/adresses', SitesController::class . ':adresses');
        $group->post('/add', SitesController::class.':add');
        $group->get('/delete', SitesController::class.':delete');
        $group->post('/update', SitesController::class.':update');
    })->add(new AuthenticationMiddleware());

    $app->group('/contratVacataire', function (Group $group) {
        $group->get('', ContratVacataireController::class.':index');
        $group->post('/add', ContratVacataireController::class.':add');
        $group->get('/delete', ContratVacataireController::class.':delete');
        $group->post('/update', ContratVacataireController::class.':update');
    })->add(new AuthenticationMiddleware());

    $app->get('/aboutus', IndexController::class . ':aboutus')->add(new AuthenticationMiddleware());

    $app->group('/vue', function(Group $group){
        $group->get('/entpCourrier', VueController::class . ':entpCourrier');
        $group->get('/entpsBlocked', VueController::class . ':entpBlocked');
        $group->get('/vacMds', VueController::class . ':vacMds');
        $group->get('/vacMdsCurrent', VueController::class . ':vacMdsCurrent');
        $group->get('/entpNbByContrat', VueController::class . ':entpNbByContrat');
        $group->get('/laboContrat', VueController::class . ':laboContrat');
        $group->get('/mds', VueController::class . ':mds');
        $group->get('/vacataire', VueController::class . ':vacataire');
        $group->get('/alternant', VueController::class . ':alternant');
        $group->get('/stagiaire', VueController::class . ':stagiaire');
        $group->get('/etudiant', VueController::class . ':etudiant');
        $group->get('/conflict', VueController::class . ':conflict');
        $group->get('/yearStatEntps', VueController::class . ':yearStatEntps');
        $group->get('/conflitEtudiant', VueController::class . ':conflitEtudiant');
        $group->get('/conflitEtudiant2', VueController::class . ':conflitEtudiant2');
        $group->get('/mdsNote', VueController::class . ':mdsNote');
    })->add(new AuthenticationMiddleware());

    $app->group('/laboratoire', function(Group $group){
        $group->get('', LaboratoireController::class . ':liste');
        $group->get('/liste', LaboratoireController::class . ':liste');
        $group->get('/add', LaboratoireController::class . ':add' );
        $group->get('/delete', LaboratoireController::class . ':delete' );
        $group->get('/update', LaboratoireController::class . ':update');
    })->add(new AuthenticationMiddleware());

    $app->group('/entreprise', function(Group $group){
        $group->get('', EntrepriseController::class . ':liste');
        $group->get('/liste', EntrepriseController::class . ':liste');
        $group->get('/delete', EntrepriseController::class . ':delete');
        $group->post('/add', EntrepriseController::class . ':add');
        $group->post('/update', EntrepriseController::class . ':update');
    })->add(new AuthenticationMiddleware());

    $app->group('/employe', function(Group $group){
        $group->get('', EmployeController::class . ':index');
        $group->get('/delete', EmployeController::class . ':delete');
        $group->post('/add', EmployeController::class . ':add');
        $group->post('/update', EmployeController::class . ':update');
    })->add(new AuthenticationMiddleware());
};
