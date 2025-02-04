<?php
namespace App\Application\Models;
use App\Application\Model;

class Employe extends Model {
    protected static $table = "employe";
    protected static $primary_key = ["nom", "prenom"];
    protected string $nom;
    protected string $prenom;

    /**
     * @param string $nom
     * @param string $prenom
     */
    public function __construct(string $nom, string $prenom) {
        $this->nom = $nom;
        $this->prenom = $prenom;
        parent::__construct();
    }

    /**
     * @return string
     */
    public function getNom(): string {
        return $this->nom;
    }

    /**
     * @return string
     */
    public function getPrenom(): string {
        return $this->prenom;
    }

    /**
     * @param String $nom
     * @return string
     */
    public function setNom(String $nom): void {
        $this->prenom = $nom;
    }

    public function  setPrenom(String $prenom): void {
        $this->prenom = $prenom;
    }

    /**
     * @param int $limit
     * @param string|null $orderBy
     * @param int $order
     * @param int $offset
     * @return array
     * @throws \Exception
     */
    public static function getAll(int $limit = -1, string $orderBy = null, int $order = 1, int $offset = -1): array
    {
        $res = parent::getAll($limit, $orderBy, $order);
        $results = [];
        foreach ($res as $r) {
            $results[] = new Employe($r['nom'], $r['prenom']);
        }
        return $results;
    }

    /**
     * @param array $keys
     * @return Employe|null
     * @throws \Exception
     */
    public static function get(array $keys): Employe | null
    {
        $employe = parent::get($keys);
        if ($employe) {
            return new Employe($employe['nom'], $employe['prenom']);
        }
        return null;
    }
}