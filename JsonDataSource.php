<?php

use exceptions\SchemaDoesNotMatch;
use exceptions\TableDoesNotExists;

class JsonDataSource
{

    private array $data_source;
    private static array $instances = [];

    private function __construct(private readonly string $path)
    {
    }

    /**
     * Singleton. It won't return new instance if already initialised with the same path
     * @param $path - path to JSON file
     * @return JsonDataSource
     */
    public static function getInstance($path): JsonDataSource
    {
        $full_path = realpath($path);
        if (array_key_exists(realpath($path), self::$instances)) {
            return self::$instances[$full_path];
        }
        $new_instance = new self($full_path);
        $new_instance->load();

        self::$instances[$full_path] = $new_instance;

        return self::$instances[$full_path];

    }

    /**
     * Loads database from file
     * @throws JsonException
     */
    private function load(): void
    {
        $json = file_get_contents($this->path);
        $this->data_source = json_decode($json, true, flags: JSON_THROW_ON_ERROR);
    }

    /**
     * Inserts new row in the table, updates auto-increment value
     * @throws TableDoesNotExists
     * @throws SchemaDoesNotMatch
     */
    function insert($table_name, $data): void
    {
        $this->validateData($table_name, $data);

        //increment id
        $new_insert_id = $this->incrementLastInsertId($table_name);


        //update id value in data
        $iteration = 0;
        foreach ($this->getSchema($table_name) as $value) {
            if ($value === "auto_increment") {
                $data[$iteration] = $new_insert_id;
            }
        }

        //insert values in table
        $this->data_source["tables"][$table_name]["values"][$new_insert_id] = $data;
        $this->writeToDisk();
    }

    private function incrementLastInsertId($table_name): int
    {
        $last_insert_id = &$this->data_source["tables"][$table_name]["last_insert_id"];
        $last_insert_id += 1;
        return $last_insert_id;
    }

    /**
     * Validates user data with table schema
     * @throws TableDoesNotExists
     * @throws SchemaDoesNotMatch
     */
    private function validateData($table_name, array $data): void
    {
        $this->tableExistsOrError($table_name);

        $schema = $this->getSchema($table_name);
        if (count($schema) !== count($data)) throw new SchemaDoesNotMatch();

        $iteration = 0;
        foreach ($schema as $column => $data_type) {
            $given_type = gettype($data[$iteration]);
            if ($data_type === "auto_increment") {
                if (!is_null($data[$iteration])) {
                    throw new SchemaDoesNotMatch("'$column' column should be null, $given_type given");
                }
            }
            if ($data_type === "int" and !is_int($data[$iteration])) {
                throw new SchemaDoesNotMatch("'$column' column should be integer, $given_type given");
            }
            if ($data_type === "string" and !is_string($data[$iteration])) {
                throw new SchemaDoesNotMatch("'$column' column should be string, $given_type given");
            }

            $iteration += 1;
        }

    }

    /**
     * @throws TableDoesNotExists
     */
    function getSchema($table_name)
    {
        $this->tableExistsOrError($table_name);
        return $this->data_source["tables"][$table_name]["schema"];
    }

    /**
     * Returns array of column names
     * @throws TableDoesNotExists
     */
    function getColumns($table_name): array
    {
        return array_keys($this->getSchema($table_name));
    }

    /**
     * @param $table_name
     * @return array - Все строки из БД
     */
    function getAll($table_name): array
    {
        return $this->data_source["tables"][$table_name]["values"];
    }

    /**
     * @throws TableDoesNotExists
     */
    function tableExistsOrError($table_name): true
    {
        if (!array_key_exists($table_name, $this->data_source["tables"])) {
            throw new TableDoesNotExists($table_name . " does not exists");
        }

        return true;
    }

    function findOneBy($table_name, $column_name, $value)
    {
        $data = $this->getAll($table_name);
        $column_key = $this->getColumnKeyByName($table_name, $column_name);
        foreach ($data as $row) {
            if ($row[$column_key] === $value) return $row;
        }

        return false;
    }

    function deleteOneBy($table_name, $column_name, $value): bool
    {
        $data = &$this->data_source["tables"][$table_name]["values"];
        $column_key = $this->getColumnKeyByName($table_name, $column_name);

        foreach ($data as $key => &$row) {
            if ($row[$column_key] === $value) {
                unset($data[$key]);
                $this->writeToDisk();
                return true;
            }
        }
        return false;
    }

    /**
     * Returns column key as number
     * @param $table_name
     * @param $column_name
     * @return false|int
     * @throws TableDoesNotExists
     */
    function getColumnKeyByName($table_name, $column_name): false|int
    {
        return array_search($column_name, $this->getColumns($table_name));
    }


    /**
     * Writes database as JSON file on disk
     */
    private function writeToDisk(): void
    {
        file_put_contents($this->path, json_encode($this->data_source, JSON_PRETTY_PRINT));
    }
}