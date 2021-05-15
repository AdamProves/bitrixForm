<?php

class Mysql
{
    protected string $password;
    protected string $userName;
    protected string $database;
    protected string $table;
    protected PDO $pdo;

    public function __construct(array $config)
    {
        $this->password = $config['password'];
        $this->userName = $config['user'];
        $this->table = $config['table'];
        $this->database = $config['database'];

        $this->connect();
    }

    protected function connect(): void
    {
        $dsn = sprintf('mysql:dbname=%s;host=127.0.0.1', $this->database);
        $this->pdo = new PDO($dsn, $this->userName, $this->password, [PDO::ATTR_CASE => PDO::CASE_LOWER]);
    }

    public function createAbiturient(
        int $iin,
        string $firstName,
        string $lastName
    ): array {
        $params = [
            $iin,
            $firstName,
            $lastName,
        ];

        $prepared = $this->pdo->prepare($this->getPrepareInsert());
        $prepared->execute($params);

        return $this->selectByIin($iin);
    }

    protected function getPrepareInsert(): string
    {
        return sprintf(
            'INSERT INTO `%s` (iin, first_name, last_name) VALUES (?, ?, ?)',
            $this->table
        );
    }

    public function selectByIin(int $iin): array
    {
        $prepared = $this->pdo->prepare($this->getPrepareSelectIin());
        $prepared->execute([$iin]);
        return $prepared->fetchAll(PDO::FETCH_ASSOC);
    }

    protected function getPrepareSelectIin(): string
    {
        return sprintf(
            'SELECT id FROM `%s`.`%s` WHERE `iin` = ? LIMIT 1',
            $this->database,
            $this->table
        );
    }

    public function totalCount(): int
    {
        $sql = sprintf('SELECT COUNT(*) as cnt FROM `%s`', $this->table);
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC)[0]['cnt'];
    }
}