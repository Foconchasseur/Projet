<?php

namespace App\Application\Models;

class ContratEtudiant extends ContratSite {
    private string $type;
    private int $noteMaitre;
    private int $noteEntreprise;
    private Employe $maitre;
    private Etudiant $etudiant;

    public function __construct(string $dateDebut, string $dateFin, Entreprise $entreprise, Site $site, ?string $dateFinAnticipe, )
    {
        parent::__construct($dateDebut, $dateFin, $entreprise, $site, $dateFinAnticipe);
    }

    /**
     * @param int $limit
     * @param string|null $orderBy
     * @param int $order
     * @return array
     */
    public static function getAll(int $limit = 50, string $orderBy = null, int $order = 1): array
    {
        // TODO: Implement getAll() method.
    }

    /**
     * @param array $keys
     * @return mixed
     */
    public static function get(array $keys): mixed
    {
        // TODO: Implement get() method.
    }

    /**
     * @return bool
     */
    public function save(): bool
    {
        // TODO: Implement save() method.
    }

    /**
     * @return bool
     */
    public function delete(): bool
    {
        // TODO: Implement delete() method.
    }
}