<?php
namespace App\Application\Models;

use App\Application\Authenticable;
use App\Application\Model;

class Utilisateur extends Model {
    use Authenticable;

    protected static $table = "utilisateur";
    protected static $primary_key = "login";
    protected string $nom;
    protected string $prenom;
    protected string $login;
    protected string $password;

    /**
     * @param string $nom
     * @param string $prenom
     * @param string $username
     * @param string $password
     */
    public function __construct(string $nom, string $prenom, string $username, string $password) {
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->login = $username;
        $this->password = $password;
        parent::__construct();
    }

    /**
     * @return string
     */
    public function getNom(): string
    {
        return $this->nom;
    }

    /**
     * @return string
     */
    public function getPrenom(): string
    {
        return $this->prenom;
    }

    /**
     * @return string
     */
    public function getLogin(): string
    {
        return $this->login;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $nom
     */
    public function setNom(string $nom): void
    {
        $this->nom = $nom;
    }

    /**
     * @param string $login
     */
    public function setLogin(string $login): void
    {
        $this->login = $login;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * @param string $prenom
     */
    public function setPrenom(string $prenom): void
    {
        $this->prenom = $prenom;
    }

    /**
     * @param array $keys
     * @return Utilisateur|null
     * @throws \Exception
     */
    public static function get(array $keys): Utilisateur | null
    {
        $user = parent::get($keys);
        if ($user) return new Utilisateur($user['nom'], $user['prenom'], $user['login'], $user['password']);
        return null;
    }

    /**
     * @param int $limit
     * @param string|null $orderBy
     * @param int $order
     * @param int $offset
     * @return Utilisateur[]
     * @throws \Exception
     */
    public static function getAll(int $limit = 50, string $orderBy = null, int $order = 1, int $offset = -1): array
    {
        $res = parent::getAll($limit, $orderBy, $order, $offset);
        $results = [];
        foreach ($res as $result) {
            $results[] = new Utilisateur($result['nom'], $result['prenom'], $result['login'], $result['password']);
        }
        return $results;
    }
}