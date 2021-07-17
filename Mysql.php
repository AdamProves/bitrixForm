<?php

class Mysql
{
    protected string $password;
    protected string $userName;
    protected string $database;
    protected string $table;
    protected array $config;
    protected PDO $pdo;

    public function __construct(array $config)
    {
        $this->password = $config['password'];
        $this->userName = $config['user'];
        $this->database = $config['database'];
        $this->config = $config;

        $this->connect();
    }

    /**
     * Устанваливает коннект с бд
     */
    protected function connect(): void
    {
        $dsn = sprintf('mysql:dbname=%s;host=127.0.0.1', $this->database);
        $this->pdo = new PDO($dsn, $this->userName, $this->password, [PDO::ATTR_CASE => PDO::CASE_LOWER]);
    }

    /**
     * Метод для создания записи абитуриента в бд
     * @param string $iin
     * @param string $firstName
     * @param string $lastName
     * @return array
     */
    public function createAbiturient(
        string $iin,
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

    /**
     * Возвращает подготовленный запрос на insert
     * @return string
     */
    protected function getPrepareInsert(): string
    {
        return sprintf(
            'INSERT INTO `%s` (iin, first_name, last_name) VALUES (?, ?, ?)',
            $this->table
        );
    }

    /**
     * Метод делает выборку по ИИН'у
     * @param string $iin
     * @return array
     */
    public function selectByIin(string $iin): array
    {
        $prepared = $this->pdo->prepare($this->getPrepareSelectIin());
        $prepared->execute([$iin]);
        return $prepared->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Возвращает подготовленный запрос для поиска по иину
     * @return string
     */
    protected function getPrepareSelectIin(): string
    {
        return sprintf(
            'SELECT id FROM `%s`.`%s` WHERE `iin` = ? LIMIT 1',
            $this->database,
            $this->table
        );
    }

    /**
     * Возвращает количество записей из таблицы
     * @return int
     */
    public function totalCount(): int
    {
        $sql = sprintf('SELECT COUNT(*) as cnt FROM `%s`', $this->table);
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC)[0]['cnt'];
    }

    /**
     * Устанавливает тип таблицы
     * @param string $type
     */
    public function setTableType(string $type): void
    {
        $this->table = $this->config['table'][$type] ?? '';
    }
}