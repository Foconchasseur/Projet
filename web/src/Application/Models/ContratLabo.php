<?php
namespace App\Application\Models;

class ContratLabo extends ContratSite {
    protected static $table = 'contrat_labo';
    protected static $primary_key = 'id';
    private readonly Laboratoire $laboratoire;

    /**
     * @param string $dateDebut
     * @param string $dateFin
     * @param Entreprise $entreprise
     * @param Site $site
     * @param string|null $dateFinAnticipe
     * @param Laboratoire $laboratoire
     */
    public function __construct(string $dateDebut, string $dateFin, Entreprise $entreprise, Site $site, ?string $dateFinAnticipe, Laboratoire $laboratoire) {
        parent::__construct($dateDebut, $dateFin, $entreprise, $site, $dateFinAnticipe);
        $this->laboratoire = $laboratoire;
    }


    /**
     * @return Laboratoire
     */
    public function getLaboratoire(): Laboratoire
    {
        return $this->laboratoire;
    }

    public static function get(array $keys): ?ContratLabo {
        $contrat = parent::get($keys);
        if($contrat) return new ContratLabo(
            $contrat['date_debut'],
            $contrat['date_fin'],
            Entreprise::get(['nom'=> $contrat['entreprise']]),
            Site::get([
                'pays'=> $contrat['pays_site'],
                'ville' => $contrat['ville_site'],
                'adresse'=> $contrat['adresse_site']
            ]),
            $contrat['date_fin_anticipe'],
            Laboratoire::get(['nom' => $contrat['laboratoire']])
        );
        return null;
    }
}