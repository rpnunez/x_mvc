<?php

namespace XMVC\Service;

use PDO;

/**
 * Database service for managing PDO connections.
 */
class Db
{
    /**
     * The active PDO connection.
     *
     * @var PDO|null
     */
    protected $pdo;

    /**
     * The application configuration service.
     *
     * @var Config
     */
    protected $config;

    /**
     * Db constructor.
     *
     * @param Config $config The application configuration service.
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Get the active PDO connection.
     *
     * If a connection does not exist, it will be created.
     *
     * @return PDO The active PDO connection.
     */
    public function pdo()
    {
        if ($this->pdo) {
            return $this->pdo;
        }

        $dbConfig = $this->config->get('db.connections.' . $this->config->get('db.default'));

        $dsn = "mysql:host={$dbConfig['host']};dbname={$dbConfig['database']};charset={$dbConfig['charset']}";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        $this->pdo = new PDO($dsn, $dbConfig['username'], $dbConfig['password'], $options);

        return $this->pdo;
    }
}