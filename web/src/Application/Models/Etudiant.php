<?php

namespace App\Application\Models;

use App\Application\Model;
use Exception;

class Etudiant extends Model
{
    protected static $table = "etudiant";
    protected static $primary_key = "no_etu";
    protected string $nationalite;
    protected string $no_etu;

    /**
     * @param string $nationalite
     * @param string $no_etu
     */
    public function __construct(string $nationalite, string $no_etu)
    {
        $this->nationalite = $nationalite;
        $this->no_etu = $no_etu;
        parent::__construct();
    }

    /**
     * @return string
     */
    public function getNationalite(): string
    {
        return $this->nationalite;
    }

    /**
     * @return string
     */
    public function getNoEtu(): string
    {
        return $this->no_etu;
    }

    /**
     * @param string $nationalite
     */
    public function setNationalite(string $nationalite): void
    {
        $this->nationalite = $nationalite;
    }

    /**
     * @param string $no_etu
     */
    public function setNoEtu(string $no_etu): void
    {
        $this->no_etu = $no_etu;
    }

    /**
     * @param array $keys
     * @return Etudiant|null
     * @throws Exception
     */
    public static function get(array $keys): Etudiant | null
    {
        $etudiant = parent::get($keys);
        $results = static::getAll(50, is_array(self::$primary_key) ? self::$primary_key[0] : self::$primary_key, 1, ($limit - 1) * $page);
        if ($etudiant) {
            return new Etudiant($etudiant["nationalite"], $etudiant["no_etu"]);
        } else {
            return null;
        }
    }

    /**
     * @param int $limit
     * @param string|null $orderBy
     * @param int $order
     * @return Etudiant[]
     * @throws Exception
     */
    public static function getAll(int $limit = -1, string $orderBy = null, int $order = 1, int $offset = 0): array
    {
        $res = parent::getAll($limit, $orderBy, $order, $offset);
        $results = [];
        foreach ($res as $r) {
            $results[] = new Etudiant($r['nationalite'], $r['no_etu']);
        }
        return $results;
    }
}
