<?php
namespace App\Application\Models;

use App\Application\Model;
use Exception;

class Site extends Model {
    protected static $table = "site";
    protected static $primary_key = ["pays", "ville", "adresse"];
    protected bool $ouvert;
    protected string $pays;
    protected string $ville;
    protected string $adresse;

    /**
     * @param bool $ouvert
     * @param string $pays
     * @param string $ville
     * @param string $adresse
     */
    public function __construct(bool $ouvert, string $pays, string $ville, string $adresse) {
        $this->ouvert = $ouvert;
        $this->pays = $pays;
        $this->ville = $ville;
        $this->adresse = $adresse;
        parent::__construct();
    }

    /**
     * @return bool
     */
    public function isOuvert(): bool {
        return $this->ouvert;
    }

    /**
     * @return string
     */
    public function getPays(): string {
        return $this->pays;
    }

    /**
     * @return string
     */
    public function getVille(): string {
        return $this->ville;
    }

    /**
     * @return string
     */
    public function getAdresse(): string {
        return $this->adresse;
    }

    /**
     * @param bool $ouvert
     */
    public function setOuvert(bool $ouvert): void
    {
        $this->ouvert = $ouvert;
    }

    /**
     * @param string $adresse
     */
    public function setAdresse(string $adresse): void
    {
        $this->adresse = $adresse;
    }

    /**
     * @param string $pays
     */
    public function setPays(string $pays): void
    {
        $this->pays = $pays;
    }

    /**
     * @param string $ville
     */
    public function setVille(string $ville): void
    {
        $this->ville = $ville;
    }

    /**
     * @param array $keys
     * @return Site|null
     * @throws Exception
     */
    public static function get(array $keys): Site | null
    {
        $site = parent::get($keys);
        if ($site) {
            return new Site($site['ouvert'], $site['pays'], $site['ville'], $site['adresse']);
        }
        return null;
    }

    /**
     * @param int $limit
     * @param string|null $orderBy
     * @param int $order
     * @param int $offset
     * @return Site[]
     * @throws Exception
     */
    public static function getAll(int $limit = 50, string $orderBy = null, int $order = 1, int $offset = -1): array
    {
        $res = parent::getAll($limit, $orderBy, $order, $offset);
        $sites = [];
        foreach ($res as $site) {
            $sites[] = new Site($site['ouvert'], $site['pays'], $site['ville'], $site['adresse']);
        }
        return $sites;
    }
}