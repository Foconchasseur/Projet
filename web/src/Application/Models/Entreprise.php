<?php

namespace App\Application\Models;

use App\Application\Database;
use App\Application\Model;
use Exception;

class Entreprise extends Model {
    protected static $table = 'entreprise';
    protected static $primary_key = 'nom';
    protected Site $siege;
    protected bool $ouvert;
    protected string $nom;
    protected int $effectif;

    /**
     * @param Site $siege
     * @param bool $ouvert
     * @param string $nom
     * @param int $effectif
     */
    public function __construct(Site $siege, bool $ouvert, string $nom, int $effectif) {
        parent::__construct();
        $this->siege = $siege;
        $this->ouvert = $ouvert;
        $this->nom = $nom;
        $this->effectif = $effectif;
    }

    /**
     * @return Site
     */
    public function getSiege(): Site {
        return $this->siege;
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
     * @param Site $siege
     */
    public function setSiege(Site $siege): void {
        $this->siege = $siege;
    }

    /**
     * @param bool $ouvert
     */
    public function setOuvert(bool $ouvert): void {
        $this->ouvert = $ouvert;
    }

    /**
     * @return int
     */
    public function getEffectif(): int
    {
        return $this->effectif;
    }

    /**
     * @param int $effectif
     */
    public function setEffectif(int $effectif): void
    {
        $this->effectif = $effectif;
    }

    public static function getAll(int $limit = 50, string $orderBy = null, int $order = 1, int $offset = -1): array {
        $pdo = Database::getInstance();
        $request = "SELECT nom, e.ouvert, pays_siege, ville_siege, adresse_siege, effectif, s.ouvert as ouvert_siege FROM entreprise AS e INNER JOIN site AS s ON e.pays_siege=s.pays AND e.ville_siege=s.ville AND e.adresse_siege=s.adresse";
        if ($orderBy !== null) {
            $request .= " ORDER BY UPPER(".$orderBy.")"." ".($order > 0 ? 'ASC' : 'DESC');
        }
        $args = [];
        if ($limit >= 0) {
            $request .= " LIMIT ?";
            $args[] = $limit;
            if ($offset !== -1) {
                $request .= " OFFSET ?";
                $args[] = $offset;
            }
        }
        $req = $pdo->prepare($request);
        $req->execute($args);
        $res = $req->fetchAll();
        $enterprises = [];
        foreach ($res as $entreprise) {
            $site = new Site($entreprise['ouvert_siege'], $entreprise['pays_siege'], $entreprise['ville_siege'], $entreprise['adresse_siege']);
            $enterprises[] = new Entreprise($site, $entreprise['ouvert'], $entreprise['nom'], $entreprise['effectif']);
        }
        return $enterprises;
    }

    public static function get(array $keys): ?Entreprise {
        $entreprise = parent::get($keys);
        if ($entreprise) {
            $entreprise = new Entreprise(
                Site::get(['pays' => $entreprise['pays_siege'], 'ville' => $entreprise['ville_siege'], 'adresse' => $entreprise['adresse_siege']]),
                $entreprise['ouvert'],
                $entreprise['nom'],
                $entreprise['effectif']
            );
            $params = get_object_vars($entreprise);
            array_shift($params);
            $entreprise->setContext($params);
            return $entreprise;
        }



        return null;
    }

    /**
     * @throws Exception
     */
    public function delete(): bool {
        $db = Database::getInstance();
        if (
            ContratEtudiant::get(['entreprise' => $this->nom]) or
            ContratLabo::get(['entreprise' => $this->nom]) or
            ContratVacataire::get(['entreprise' => $this->nom]) or
            Donation::get(['entreprise' => $this->nom])
        ) throw new Exception("Cette entreprise a des contrats enregistrés");
        if (!empty(
        $db->query("select * from entreprise_site where nom_entreprise=" . $db->quote($this->getNom()))
            ->fetchAll()
        )) throw new Exception("Cette entreprise est liée a des locaux (sites)");

        return $db->prepare("delete from entreprise where nom=" . $db->quote($this->nom))->execute();
    }

    /**
     * @return bool false on failure
     */
    public function save(): bool {
        try {
            $pdo = Database::getInstance();

            $old = self::get(['nom' => $this->getContext()['nom']??""]);

            $nom = $pdo->quote($this->nom);
            $pays_siege = $pdo->quote($this->siege->getPays());
            $ville_siege = $pdo->quote($this->siege->getVille());
            $adresse_siege = $pdo->quote($this->siege->getAdresse());
            $ouvert = $pdo->quote($this->ouvert ? "true" : "false");
            $effectif = $this->effectif;

            if (!$old) { // If it doesn't exist insert it
                $req = $pdo->prepare("INSERT INTO entreprise values(
                              $nom,
                              $pays_siege,
                              $ville_siege,
                              $adresse_siege,
                              $ouvert,
                              $effectif)"
                );
            } else {
                $req = $pdo->prepare("update entreprise
                set nom=$nom,
                    pays_siege=$pays_siege,
                    ville_siege=$ville_siege,
                    adresse_siege=$adresse_siege,
                    ouvert=$ouvert,
                    effectif=$effectif
                where nom='$old->nom'");
            }

            $executed = $req->execute();

            $params = get_object_vars($this);
            array_shift($params);
            if($executed) $this->setContext($params);
            return $executed;
        } catch (Exception) {
            return false;
        }
    }

    /**
     * @return false|string
     */
    public function toJson(){
        return json_encode([
            'nom' => $this->nom,
            'pays_siege' => $this->siege->getPays(),
            'ville_siege' => $this->siege->getVille(),
            'adresse_siege' => $this->siege->getAdresse(),
            'ouvert' => $this->isOuvert(),
            'effectif' => $this->effectif
        ]);
    }
}