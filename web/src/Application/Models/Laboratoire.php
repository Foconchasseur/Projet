<?php
namespace App\Application\Models;
use App\Application\Model;
use Exception;

class Laboratoire extends Model {
    protected static $table = 'laboratoire';
    protected static $primary_key = 'nom';
    protected string $nom;

    /**
     * @param string $nom
     */
    public function __construct(string $nom) {
        $this->nom = $nom;
        parent::__construct();
    }

    /**
     * @return string
     */
    public function getNom(): string {
        return $this->nom;
    }

    /**
     * @param string $nom
     */
    public function setNom(string $nom): void {
        $this->nom = $nom;
    }

    /**
     * @param array $keys
     * @return Laboratoire|null
     * @throws Exception
     */
    public static function get(array $keys): ?Laboratoire
    {
        $laboratoire = parent::get($keys);
        if($laboratoire) return new Laboratoire($laboratoire["nom"]);
        return null;
    }

    /**
     * @param int $limit
     * @param string|null $orderBy
     * @param int $order
     * @return Laboratoire[]
     * @throws Exception
     */
    public static function getAll(int $limit = 50, string $orderBy = null, int $order = 1): array
    {
        $res = parent::getAll($limit, $orderBy, $order);
        $labs = [];
        foreach ($res as $site) {
            $labs[] = new Laboratoire($site['nom']);
        }
        return $labs;
    }
}