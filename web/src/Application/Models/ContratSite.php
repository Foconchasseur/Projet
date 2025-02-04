<?php
namespace App\Application\Models;

abstract class ContratSite extends Contrat{
    protected Site $site;
    protected ?string $dateFinAnticipe;

    /**
     * @param string $dateDebut
     * @param string $dateFin
     * @param Entreprise $entreprise
     * @param Site $site
     * @param string|null $dateFinAnticipe
     */
    public function __construct(string $dateDebut, string $dateFin, Entreprise $entreprise, Site $site, ?string $dateFinAnticipe) {
        $this->site = $site;
        $this->dateFinAnticipe = $dateFinAnticipe;
        parent::__construct($dateDebut, $dateFin, $entreprise);
    }


    /**
     * @return Site
     */
    public function getSite(): Site {
        return $this->site;
    }

    /**
     * @return string
     */
    public function getDateFinAnticipe(): string {
        return $this->dateFinAnticipe;
    }

    /**
     * @param Site $site
     */
    public function setSite(Site $site): void
    {
        $this->site = $site;
    }

    /**
     * @param string|null $dateFinAnticipe
     */
    public function setDateFinAnticipe(?string $dateFinAnticipe): void
    {
        $this->dateFinAnticipe = $dateFinAnticipe;
    }
    
}