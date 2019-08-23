<?php

/**
 * Class Database. Handles connections to the database, queries and result formatting
 */
class Database
{

    private $conn;

    /**
     * Database constructor. Establishes connections to the database
     */
    public function __construct()
    {

        $charset = 'utf8';

        require "../settings.php";

        $dsn = "mysql:host=$dbhost;dbname=$dbname;charset=$charset";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->conn = new \PDO($dsn, $dbuser, $dbpass, $options);
        } catch (\PDOException $e) {
            throw new \PDOException($e->getMessage(), (int)$e->getCode());
        }

    }

    /**
     * Sends simple query to the database
     *
     * @param $query
     */
    public function query($query) {

        $this->conn->query($query);

    }

    /**
     * Prepares a query that has parameters
     *
     * @param $query
     * @return bool|PDOStatement
     */
    public function prepare($query) {

        return $this->conn->prepare($query);

    }
}