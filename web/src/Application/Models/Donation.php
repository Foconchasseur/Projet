<?php
namespace App\Application\Models;

class Donation extends Contrat {
    protected static $table = "donation";
    protected static $primary_key = 'id';
    private readonly int $montant;

    /**
     * @param string $dateDebut
     * @param string $dateFin
     * @param Entreprise $entreprise
     * @param int $montant
     */
    public function __construct(string $dateDebut, string $dateFin, Entreprise $entreprise, int $montant) {
        parent::__construct($dateDebut, $dateFin, $entreprise);
        $this->montant = $montant;
    }

    /**
     * @return int
     */
    public function getMontant(): int {
        return $this->montant;
    }


    /**
     * @param array $data the data got from the database in format string key => value
     * @deprecated
     * @return Donation
     */
    public static function fromData(array $data): Donation {
        return new Donation(
            $data['date_debut'],
            $data['date_fin'],
            $data['entreprise'],
            $data['montant']
        );
    }
}