<?php

class Handler
{
    // Ссылка для добавления по api
    protected const BITRIX_URL = 'https://bitrix24.uib.kz/rest/10576/cym8ornpz8xkx58u/crm.deal.add.json';
    protected const BITRIX_B_CATEGORY_ID = 34; // Категория бакалавриата
    protected const BITRIX_M_CATEGORY_ID = 35; // Категория магистратуры
    protected const BITRIX_B_TYPE = 'bitrix_b'; // Тип категории бакалавриат
    protected const BITRIX_M_TYPE = 'bitrix_m'; // Тип категории магистратура

    // Мапа по которой определяется тип
    protected const CATEGORY_MAP = [
        self::BITRIX_B_TYPE => self::BITRIX_B_CATEGORY_ID,
        self::BITRIX_M_TYPE => self::BITRIX_M_CATEGORY_ID,
    ];

    // Подставление пола
    protected const GENDER_MAP = [
        'male' => 'Мужской',
        'female' => 'Женский',
    ];

    // Мапа перевода данных с тех что пришли на нужный
    // По сути копипаста но доп валидация не помешает //TODO можно оптимизировать
    protected const TYPE_MAP = [
        'b' => self::BITRIX_B_TYPE,
        'm' => self::BITRIX_M_TYPE,
    ];

    // Мапа по которой проверяются пришедшие поля и подставляются id полей + тип поля
    // str = строка | int = число | email = почта | date = дата
    protected const REQUEST_MAP_ONLINE = [
        'first_name' => [
            self::BITRIX_B_TYPE => 'UF_CRM_1619527771578',
            self::BITRIX_M_TYPE => 'UF_CRM_1619527771578',
            'type' => 'str',
        ],
        'last_name' => [
            self::BITRIX_B_TYPE => 'UF_CRM_5D38356DB0BDB',
            self::BITRIX_M_TYPE => 'UF_CRM_5D38356DB0BDB',
            'type' => 'str',
        ],
        'iin' => [
            self::BITRIX_B_TYPE => 'UF_CRM_5C502158B09B7',
            self::BITRIX_M_TYPE => 'UF_CRM_5C502158B09B7',
            'type' => 'int',
        ],
        'phone' => [
            self::BITRIX_B_TYPE => 'UF_CRM_1621881002667',
            self::BITRIX_M_TYPE => 'UF_CRM_1621881002667',
            'type' => 'int',
        ],
        'email' => [
            self::BITRIX_B_TYPE => 'UF_CRM_1621881034353',
            self::BITRIX_M_TYPE => 'UF_CRM_1621881034353',
            'type' => 'email',
        ],
    ];

    protected const REQUEST_MAP_LOCAL = [
        'first_name' => [
            self::BITRIX_B_TYPE => 'UF_CRM_1619527771578',
            self::BITRIX_M_TYPE => 'UF_CRM_1619527771578',
            'type' => 'str',
        ],
        'last_name' => [
            self::BITRIX_B_TYPE => 'UF_CRM_5D38356DB0BDB',
            self::BITRIX_M_TYPE => 'UF_CRM_5D38356DB0BDB',
            'type' => 'str',
        ],
        'second_name' => [
            self::BITRIX_B_TYPE => 'UF_CRM_5D38362365A13',
            self::BITRIX_M_TYPE => 'UF_CRM_5D38362365A13',
            'type' => 'str',
        ],
        'latin_first_name' => [
            self::BITRIX_B_TYPE => 'UF_CRM_1619527843599',
            self::BITRIX_M_TYPE => 'UF_CRM_1619527843599',
            'type' => 'str',
        ],
        'latin_last_name' => [
            self::BITRIX_B_TYPE => 'UF_CRM_1619527862877',
            self::BITRIX_M_TYPE => 'UF_CRM_1619527862877',
            'type' => 'str',
        ],
        'birth_date' => [
            self::BITRIX_B_TYPE => 'UF_CRM_1619527910722',
            self::BITRIX_M_TYPE => 'UF_CRM_1619527910722',
            'type' => 'date',
        ],
        'iin' => [
            self::BITRIX_B_TYPE => 'UF_CRM_5C502158B09B7',
            self::BITRIX_M_TYPE => 'UF_CRM_5C502158B09B7',
            'type' => 'str',
        ],
        'issuer' => [
            self::BITRIX_B_TYPE => 'UF_CRM_1619527968847',
            self::BITRIX_M_TYPE => 'UF_CRM_1619527968847',
            'type' => 'str',
        ],
        'valid_from' => [
            self::BITRIX_B_TYPE => 'UF_CRM_1619527980976',
            self::BITRIX_M_TYPE => 'UF_CRM_1619527980976',
            'type' => 'date',
        ],
        'valid_to' => [
            self::BITRIX_B_TYPE => 'UF_CRM_1619527999054',
            self::BITRIX_M_TYPE => 'UF_CRM_1619527999054',
            'type' => 'date',
        ],
        'doc_num' => [
            self::BITRIX_B_TYPE => 'UF_CRM_1619528008772',
            self::BITRIX_M_TYPE => 'UF_CRM_1619528008772',
            'type' => 'int',
        ],
        'gender' => [
            self::BITRIX_B_TYPE => 'UF_CRM_1619528018107',
            self::BITRIX_M_TYPE => 'UF_CRM_1619528018107',
            'type' => 'str',
        ],
        'birth_place' => [
            self::BITRIX_B_TYPE => 'UF_CRM_1619528027995',
            self::BITRIX_M_TYPE => 'UF_CRM_1619528027995',
            'type' => 'str',
        ],
        'nation' => [
            self::BITRIX_B_TYPE => 'UF_CRM_1619528041291',
            self::BITRIX_M_TYPE => 'UF_CRM_1619528041291',
            'type' => 'str',
        ],
        'citizen' => [
            self::BITRIX_B_TYPE => 'UF_CRM_1619528048890',
            self::BITRIX_M_TYPE => 'UF_CRM_1619528048890',
            'type' => 'str',
        ],
    ];

    // Мапа по которой подставляется баркод
    // Вынесена отдельно так как не приходит в запросе, а подставляется после запроса в бд
    protected const BARCODE_MAP = [
        self::BITRIX_B_TYPE => 'UF_CRM_1621077173967',
        self::BITRIX_M_TYPE => 'UF_CRM_1621077173967',
    ];

    protected string $currentType = self::BITRIX_B_TYPE; // По умолчанию бакалавриат
    protected int $responseCode = 200; // По умолчанию вернём 200 статус
    protected array $data;
    protected array $response;
    protected array $requestData = [];
    protected Mysql $mysql;
    protected int $limit; // Лимит на количество абитуриентов, берётся из конфига

    public function __construct(array $data, Mysql $mysql, int $limit)
    {
        $type = $data['type_group'];
        unset($data['type_group']);
        $this->mysql = $mysql;
        $this->currentType = self::TYPE_MAP[$type]; // Проставляем текущий тип
        $this->mysql->setTableType($type); // Указываем нужную таблицу по контексту
        $this->data = $data;
        $this->limit = $limit;
    }

    public static function make(Mysql $mysql, int $limit): self
    {
        header('Content-type: application/json');

        try {
            $self = new self(
                json_decode(
                    file_get_contents('php://input'),
                    true,
                    512,
                    JSON_THROW_ON_ERROR
                ),
                $mysql,
                $limit
            );
        } catch (\Throwable $e) {
            http_response_code(418); // Вернём 418 если что-то пошло не так
            exit(1);
        }

        return $self;
    }

    public function handle(): void
    {
        try {
            if ($this->checkLimit()) {
                $this->setResponse(
                    [
                        'status' => 'limited',
                        'message' => 'Достигнут лимит абитуриентов в этом году',
                    ]
                )->send();
                return;
            }

            if (!$this->validDataAndGenerateRequest()) {
                $this->setStatusCode(400) // Если пришли не валидные данные скажем об этом и вернём 400 статус
                    ->setResponse(
                        [
                            'status' => 'fail',
                            'message' => 'Invalid data'
                        ]
                    )
                    ->send();
            }

            $response = $this->curlToBitrix(); // Отправим запрос в битрикс

            $this->setResponse($response)->setStatusCode(201)->send(); // 201 статус если всё ок
        } catch (\Throwable $e) {
            $this->setResponse(
                [
                    'status' => 'error',
                    'message' => 'Что-то пошло не так, попробуйте позже',
                ]
            )->setStatusCode(418)->send(); // Вернём 418 если что-то пошло не так
        }
    }

    /**
     * Проверим лимиты
     * @return bool
     */
    protected function checkLimit(): bool
    {
        // Делаем запрос на количество записей и сравниваем с текущим лимитом
        return $this->mysql->totalCount() >= $this->limit;
    }

    /**
     * Отправляет ответ и выходит
     */
    protected function send(): void
    {
        http_response_code($this->responseCode);
        echo json_encode($this->response); // Все ответы шлю в json формате
        exit(1);
    }

    /**
     * Устанавливает ответ в виде массива
     * @param array $data
     * @return $this
     */
    protected function setResponse(array $data): self
    {
        $this->response = $data;

        return $this;
    }

    /**
     * Валидирует запрос и устанавливает ответ
     * @return bool
     */
    protected function validDataAndGenerateRequest(): bool
    {
        $result = true;
        $prefixTittle = '';

        if (empty($this->data['form_type'])) { // Если не указали с какой формы пришло, значит нас наёбывают))
            $result = false;
        }

        // В зависимости от того откуда запрос подставим нужную мапу
        if ($this->data['form_type'] === 'online') {
            $prefixTittle = 'Онлайн ';
            $map = self::REQUEST_MAP_ONLINE;
            $this->requestData['UF_CRM_1621419153212'] = 758;
        } else {
            $map = self::REQUEST_MAP_LOCAL;
            $this->requestData['UF_CRM_1621419153212'] = 759;
        }

        // Сам процесс валидации
        foreach ($map as $key => $rules) {
            if (empty($this->data[$key]) && $key !== 'second_name') {
                $result = false;
            }

            if ($key === 'gender') {
                $this->data[$key] = self::GENDER_MAP[$this->data[$key]];
            }

            switch ($rules['type']) {
                case 'int':
                    $this->requestData[$rules[$this->currentType]] = (int)$this->data[$key];
                    break;
                case 'date':
                    if ($this->validateDate($this->data[$key])) {
                        $this->requestData[$rules[$this->currentType]] = (string)$this->data[$key];
                    } else {
                        $result = false;
                    }
                    break;
                case 'email':
                    if (filter_var($this->data[$key], FILTER_VALIDATE_EMAIL)) {
                        $this->requestData[$rules[$this->currentType]] = (string)$this->data[$key];
                    } else {
                        $result = false;
                    }
                    break;
                default: // Дефолтный тип это строка, её особо не провалидируешь, просто жёстко приравняем к строке
                    if (isset($rules[$this->currentType])) {
                        $this->requestData[$rules[$this->currentType]] = (string)$this->data[$key];
                    }
                    break;
            }
        }

        // Делаем запрос по ИИН'у что-бы проверить на дубль
        if (!empty($this->mysql->selectByIin((int)$this->data['iin']))) {
            $this->setResponse( // Если что-то нашли, значит такой ИИН уже есть, скажем об этом и выйдем
                [
                    'status' => 'duplicate',
                    'message' => 'Ваша заявка уже была отправлена ранее'
                ]
            )->setStatusCode(208)->send();
            $result = 0;
        }

        // Если все данные валидны отправим запрос в битрикс на создание
        if ($result) {
            $this->requestData['TITLE'] = $prefixTittle . $this->data['first_name'] . ' ' . $this->data['last_name'];
            $this->requestData['CATEGORY_ID'] = self::CATEGORY_MAP[$this->currentType];
            $this->requestData[self::BARCODE_MAP[$this->currentType]] = $this->genBarCode(
                $this->mysql->createAbiturient( // Не забываем создать запись в бд для получения id для баркода
                    (int)$this->data['iin'],
                    (string)$this->data['first_name'],
                    (string)$this->data['last_name']
                )
            );
        }

        return $result;
    }

    /**
     * Валидирует дату
     * @param $date
     * @param string $format
     * @return bool
     */
    protected function validateDate($date, $format = 'd.m.Y'): bool
    {
        $d = DateTime::createFromFormat($format, $date);

        return $d && $d->format($format) === $date;
    }

    /**
     * Устанавливает статус ответа
     * @param int $code
     * @return $this
     */
    protected function setStatusCode(int $code): self
    {
        $this->responseCode = $code;
        return $this;
    }

    /**
     * Логика для создания баркода, но пока криво и в целом не юзается
     * @param array $res
     * @return int
     */
    protected function genBarCode(array $res): int
    {
        $id = $res[0]['id'];

        switch (true) {
            case $id > 0 && $id < 10:
                $id = 000 . $id;
                break;
            case $id >= 10 && $id < 100:
                $id = 00 . $id;
                break;
            case $id >= 100 && $id < 1000:
                $id = 0 . $id;
                break;
            default:
                break;
        }
        return $id;
    }

    /**
     * Отправляет запрос в битрикс на создание сделки
     * @return string[]
     */
    protected function curlToBitrix(): array
    {
        $curl = curl_init();
        curl_setopt_array(
            $curl,
            array(
                CURLOPT_URL => self::BITRIX_URL,
                CURLOPT_POST => 1,
                CURLOPT_HEADER => 0,
                CURLOPT_RETURNTRANSFER => 1,
            )
        );

        curl_setopt(
            $curl,
            CURLOPT_POSTFIELDS,
            http_build_query(
                [
                    'fields' => $this->requestData,
                    'params' => ["REGISTER_SONET_EVENT" => "Y"],
                ]
            )
        );

        $result = json_decode(curl_exec($curl), true);
        $info = curl_getinfo($curl);
        $this->responseCode = $info['http_code'];
        curl_close($curl);

        // Если мы получили ответ больше 300 значит что-то не то
        if (empty($result['result']) && $info['http_code'] >= 300) {
            $response = ['message' => 'Произошла ошибка при отправке, обновите страницу и повторите попытку'];
        } else {
            $response = ['message' => 'Заявка успешно создана с номером ' . $result['result']];
        }

        return $response;
    }
}