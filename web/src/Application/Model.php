<?php

namespace App\Application;

use App\Application\Models\ContratVacataire;
use Exception;

abstract class Model implements IModel
{
    protected static $table;
    protected static $primary_key;
    public static int $limit = 50;
    private $context = [];

    public function __construct() {
        $ctx = get_object_vars($this);
        array_shift($ctx);
        $this->context = $ctx;
    }

    /**
     * @return array
     */
    public function getContext(): array {
        return $this->context;
    }

    /**
     * @param array $context
     */
    public function setContext(array $context): void {
        $this->context = $context;
    }

    /**
     * @param array $fields Array of field
     * @param string $separator Separator for each field
     * @return string WHERE statement
     */
    protected static function whereGenerator(array $fields, string $separator = "AND"): string
    {
        $req = [];
        foreach ($fields as $field) {
            $req[] = $field."=?";
        }
        return implode(" ".$separator." ", $req);
    }

    /**
     * Return parameters for SQL Query
     * @param array $values Array of values
     * @return array Parameters for SQL Query
     */
    protected static function paramsGenerator(array $values): array
    {
        $params = [];
        foreach ($values as $value) {
            if (is_bool($value)) {
                $params[] = ($value ? 'true' : 'false');
            }else if(strlen($value)===0){
                $params[] = null;
            } else {
                $params[] = $value;
            }
        }
        return $params;
    }

    /**
     * Get a record with attributes
     * @param array $keys
     * @return mixed
     * @throws Exception
     */
    public static function get(array $keys): mixed {
        try {
            $pdo = Database::getInstance();
            $req = $pdo->prepare("SELECT * FROM " . static::$table . " WHERE " . self::whereGenerator(array_keys($keys)));
            $req->execute(self::paramsGenerator(array_values($keys)));
            return $req->fetch();
        } catch (Exception) {
            return null;
        }
    }

    /**
     * Retrieve many records
     * @param int $limit number max of results
     * @param string|null $orderBy field to filterBy
     * @param int $order 1 for ASC -1 for DESC
     * @param int $offset
     * @return array
     * @throws Exception
     */
    public static function getAll(int $limit = -1, string $orderBy = null, int $order = 1, int $offset = -1): array {
        $pdo = Database::getInstance();
        $request = "SELECT * FROM ".static::$table;
        if ($orderBy !== null) {
            $request .= " ORDER BY ".$orderBy." ".($order > 0 ? 'ASC' : 'DESC');
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
        return $req->fetchAll();
    }

    /**
     * Count number of records
     * @return int
     * @throws Exception
     */
    public static function countAll(): int {
        $pdo = Database::getInstance();
        $req = $pdo->query("SELECT COUNT(*) FROM ".static::$table);
        return intval($req->fetch()[0]);
    }

    /**
     * Return an HTML template for pagination of results
     * @param int $limit
     * @param int $page
     * @return string
     * @throws Exception
     */
    public static function getPaginationHTML(): string {
        $nb_pages = intval(floor(self::countAll() / static::$limit));
        $html = "<div>";

        for ($i = 1; $i < $nb_pages; $i++) {
            $html .= "<a href='?page=".$i."'>".$i."</a>";
        }

        $html .= "</div>";
        return $html;
    }

    /**
     * @throws Exception
     */
    public function save(): bool
    {
        $params = get_object_vars($this);
        array_shift($params); // Trick to remove context variable
        $fields = array_keys($params);
        $values = array_values($params);
        // Check if record already exists and update it if so
        if (self::get($this->context)) {
            try {
                $pdo = Database::getInstance();
                $req = $pdo->prepare("UPDATE " . static::$table . " SET " . self::whereGenerator($fields, ", ") . " WHERE " . self::whereGenerator(array_keys($this->context)));
                $whereValues = $values;
                foreach ($this->context as $v) {
                    $whereValues[] = $v;
                }
                if ($req->execute(self::paramsGenerator($whereValues))) {
                    // Update context on success
                    $this->context = $params;
                    return true;
                }
                // Revert instance variable on fail
                foreach ($params as $k => $v) {
                    $this->{$k} = $this->context[$k];
                }

                return false;
            } catch (Exception $e) {
                return false;
            }
        } else { // If it doesn't exist insert it
            try {
                $pdo = Database::getInstance();
                $req = $pdo->prepare("INSERT INTO " . static::$table . "(".implode(",", $fields).") VALUES(".implode(',', array_fill(0, sizeof($fields), '?')).")");
                return $req->execute(self::paramsGenerator($values));
            } catch (Exception $e) {
                return false;
            }
        }
    }

    /**
     * Delete a record from the database
     * @return bool true on SUCCESS false on FAILURE
     */
    public function delete(): bool
    {
        try {
            $pdo = Database::getInstance();
            // Case primary key is a set
            if (\gettype(static::$primary_key) === "array") {
                $req = $pdo->prepare("DELETE FROM " . static::$table . " WHERE " . self::whereGenerator(static::$primary_key));
                $params = [];
                foreach (static::$primary_key as $key) {
                    $params[] = $this->{$key};
                }
                return $req->execute(self::paramsGenerator($params));
            }
            // Case if primary key is a single field
            $req = $pdo->prepare("DELETE FROM " . static::$table . " WHERE " . static::$primary_key . "=?");
            return $req->execute([$this->{static::$primary_key}]);
        } catch(Exception) {
            return false;
        }
    }
}