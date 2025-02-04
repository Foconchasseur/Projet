<?php

namespace App\Application;

/**
 * Interface that each model should implement
 */
interface IModel
{
    /**
     * Retrieve all records of a model
     * @param $limit int maximum number of records to retrieve
     * @param string|null $orderBy
     * @param int $order
     * @return array
     */
    public static function getAll(int $limit = 50, string $orderBy = null, int $order = 1, int $offset = -1): array;

    /**
     * Retrieve only one record
     * @param $keys array<string,string|numeric>
     * @return mixed
     */
    public static function get(array $keys): mixed;

    /**
     * Save modification of the model to the database aka insert or update
     * @return bool True on success false on failure
     */
    public function save(): bool;

    /**
     * deletes the model from the database
     * @return bool
     */
    public function delete(): bool;
}