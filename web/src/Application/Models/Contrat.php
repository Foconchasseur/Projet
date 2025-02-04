<?php
namespace App\Application\Models;

use App\Application\Model;

abstract class Contrat extends Model{
    protected const TYPE_CONTRAT_ALTERNANCE = "alternance";
    protected const TYPE_CONTRAT_STAGIAIRE = "stage";
    protected string $dateDebut;
    protected string $dateFin;
    protected Entreprise $entreprise;

    /**
     * @param string $dateDebut
     * @param string $dateFin
     * @param Entreprise $entreprise
     */
    public function __construct(string $dateDebut, string $dateFin, Entreprise $entreprise) {
        $this->dateDebut = $dateDebut;
        $this->dateFin = $dateFin;
        $this->entreprise = $entreprise;
        parent::__construct();
    }

    /**
     * @return string
     */
    public function getDateDebut(): string {
        return $this->dateDebut;
    }

    /**
     * @return string
     */
    public function getDateFin(): string {
        return $this->dateFin;
    }

    /**
     * @return Entreprise
     */
    public function getEntreprise(): Entreprise {
        return $this->entreprise;
    }

    /**
     * @param string $dateDebut
     */
    public function setDateDebut(string $dateDebut): void
    {
        $this->dateDebut = $dateDebut;
    }

    /**
     * @param string $dateFin
     */
    public function setDateFin(string $dateFin): void
    {
        $this->dateFin = $dateFin;
    }

    /**
     * @param Entreprise $entreprise
     */
    public function setEntreprise(Entreprise $entreprise): void
    {
        $this->entreprise = $entreprise;
    }
}