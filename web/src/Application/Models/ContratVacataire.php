<?php
namespace App\Application\Models;


use App\Application\Database;
use App\Application\Model;
use Exception;

class ContratVacataire extends ContratSite {
    protected static $table = "contrat_vacataire";
    protected int $note;
    protected Employe $vacataire;

    /**
     * @param Employe $vacataire
     */

    protected static $primary_key = ["nom_vacataire", "prenom_vacataire", "date_debut"];

    /**
     * @param string $dateDebut
     * @param string $dateFin
     * @param Entreprise $entreprise
     * @param Site $site
     * @param string $dateFinAnticipe
     * @param int $note
     * @param Employe $vacataire
     */
    public function __construct(string $dateDebut, string $dateFin, Entreprise $entreprise, Site $site, string $dateFinAnticipe, int $note, Employe $vacataire) {
        $this->note = $note;
        $this->vacataire = $vacataire;
        parent::__construct($dateDebut, $dateFin, $entreprise, $site, $dateFinAnticipe);
    }

    /**
     * @param $keys
     * @return ContratVacataire
     * @throws Exception
     * This function returns vacations contracts objects from row from the db
     */
    private static function fromKeys($keys) : ContratVacataire{
        $entreprise = Entreprise::get([
            'nom' => htmlspecialchars($keys["entreprise"])
        ]);
        return new ContratVacataire(
            $keys["date_debut"],
            $keys["date_fin"],
            $entreprise,
            Site::get([
                'pays'  =>  htmlspecialchars($keys["pays_site"] ?? ""),
                'ville' =>  htmlspecialchars($keys["ville_site"] ?? ""),
                'adresse' => htmlspecialchars($keys["adresse_site"] ?? "")
            ]),
            $keys['date_fin_anticipe'] ?: "",
            $keys['note']?: -1,
            Employe::get([
                'nom' => htmlspecialchars($keys["nom_vacataire"] ?? ""),
                'prenom' => htmlspecialchars($keys["prenom_vacataire"] ?? "")
            ])
        );
    }

    /**
     * @return int
     */
    public function getNote(): int {
        return $this->note;
    }

    /**
     * @param int $note
     * @throws Exception
     */
    public function setNote(int $note): void {
        if($note>10) throw new Exception("note can't be more than 10");
        $this->note = $note;
    }

    /**
     * @return Employe
     */
    public function getVacataire(): Employe {
        return $this->vacataire;
    }

    public function setVacataire(Employe $vacataire): void
    {
        $this->vacataire = $vacataire;
    }

    /**
     * @param int $limit
     * @param string|null $orderBy
     * @param int $order
     * @param int $offset
     * @return array
     * @throws Exception
     * This function is used to get all the contracts from the db
     */
    public static function getAll(int $limit = -1, string $orderBy = null, int $order = 1, int $offset = -1): array
    {
        $pdo = Database::getInstance();
        $request = "SELECT * FROM ".static::$table;
        if ($orderBy !== null) {
            $request .= " ORDER BY ".$orderBy." ".($order > 0 ? 'ASC' : 'DESC');
        }
        if ($limit >= 0) {
            $req = $pdo->prepare($request." LIMIT ?");
            $req->execute([$limit]);
        } else {
            $req = $pdo->prepare($request);
            $req->execute();
        }
        $res = $req->fetchAll();
        $contrats = [];
        foreach ($res as $keys){
            $contrats[] = ContratVacataire::fromKeys($keys);
        }
        return $contrats;
    }

    /**
     * @param array $keys
     * @return ContratVacataire|null
     * @throws Exception
     */
    public static function get(array $keys): ?ContratVacataire
    {
        $contrat =  parent::get($keys);
        if ($contrat) return self::fromKeys($contrat);
        return null;
    }

    /**
     * @return bool
     * This function deletes the current contrat from the DataBase
     */
    public function delete(): bool
    {
        try {
            $pdo = Database::getInstance();
            // Case primary key is a set
            $req = $pdo->prepare("DELETE FROM " . static::$table . " WHERE nom_vacataire=? AND prenom_vacataire=? AND date_debut=?");
            return $req->execute([$this->vacataire->getNom(),$this->vacataire->getPrenom(),$this->getDateDebut()]);

        } catch(\Exception $e) {
            return false;
        }
    }

    /**
     * @return bool
     * @throws Exception
     * This function allow us tu update or save contract of vacations
     */
    public function save(): bool
    {
        $params = get_object_vars($this);
        array_shift($params); // Trick to remove context variable
        $values = array_values($params);
        // Check if record already exists and update it if so
        if (self::get([
            'nom_vacataire'=>$this->getContext()["vacataire"]->getNom(),
            'prenom_vacataire'=>$this->getContext()["vacataire"]->getPrenom(),
            'date_debut'=>$this->getContext()["dateDebut"]
        ])) {
            try {
                $pdo = Database::getInstance();
                $req = $pdo->prepare("UPDATE contrat_vacataire SET 
                             prenom_vacataire=?, 
                             nom_vacataire=?, 
                             date_debut=?, 
                             date_fin=?, 
                             date_fin_anticipe=?,
                             entreprise=?,
                             pays_site=?,
                             ville_site=?,
                             adresse_site=?,
                             note=?
                         WHERE date_debut=? and prenom_vacataire=? and nom_vacataire=?");
                $whereValues = $values;
                foreach ($this->getContext() as $v) {
                    $whereValues[] = $v;
                }
                if ($req->execute(Model::paramsGenerator([
                    $this->getVacataire()->getPrenom(),
                    $this->getVacataire()->getNom(),
                    $this->getDateDebut(),
                    $this->getDateFin(),
                    $this->getDateFinAnticipe(),
                    $this->getEntreprise()->getNom(),
                    $this->getSite()->getPays(),
                    $this->getSite()->getVille(),
                    $this->getSite()->getAdresse(),
                    $this->getNote(),
                    $this->getContext()["dateDebut"],
                    $this->getContext()["vacataire"]->getPrenom(),
                    $this->getContext()["vacataire"]->getNom()
                    ]))) {
                    // Update context on success
                    $this->setContext($params);
                    return true;
                }
                // Revert instance variable on fail
                foreach ($params as $k => $v) {
                    $this->{$k} = $this->getContext()[$k];
                }
                return false;
            } catch (Exception $e) {
                return false;
            }
        } else { // If it doesn't exist insert it
            try {
                $pdo = Database::getInstance();
                $req = $pdo->prepare("INSERT INTO contrat_vacataire (
                               prenom_vacataire,
                               nom_vacataire,
                               date_debut,
                               date_fin,
                               date_fin_anticipe,
                               entreprise,
                               pays_site,
                               ville_site,
                               adresse_site,
                               note
                               ) VALUES (?,?,?,?,?,?,?,?,?,?);
                ");
                return $req->execute([
                    $this->getVacataire()->getPrenom(),
                    $this->getVacataire()->getNom(),
                    $this->getDateDebut(),
                    $this->getDateFin(),
                    $this->getDateFinAnticipe() === "" ? null : $this->getDateFinAnticipe(),
                    $this->getEntreprise()->getNom(),
                    $this->getSite()->getPays(),
                    $this->getSite()->getVille(),
                    $this->getSite()->getAdresse(),
                    $this->getNote() === -1 ? null : $this->getNote()
                ]);
            } catch (Exception $e) {
                return false;
            }
        }
    }
}