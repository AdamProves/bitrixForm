<?php

class Handler
{
    protected const BITRIX_URL = 'https://bitrix24.uib.kz/rest/10576/cym8ornpz8xkx58u/crm.deal.add.json';
    protected const BITRIX_B_CATEGORY_ID = 34;
    protected const BITRIX_M_CATEGORY_ID = 35;
    protected const BITRIX_B_TYPE = 'bitrix_b';
    protected const BITRIX_M_TYPE = 'bitrix_m';

    protected const CATEGORY_MAP = [
        self::BITRIX_B_TYPE => self::BITRIX_B_CATEGORY_ID,
        self::BITRIX_M_TYPE => self::BITRIX_M_CATEGORY_ID,
    ];

    protected const GENDER_MAP = [
        'male' => 'Мужской',
        'female' => 'Женский',
    ];

    protected const TYPE_MAP = [
        'b' => self::BITRIX_B_TYPE,
        'm' => self::BITRIX_M_TYPE,
    ];

    protected const REQUEST_MAP_ONLINE = [
        'first_name' => [
            self::BITRIX_B_TYPE => 'UF_CRM_1619527771578',
            self::BITRIX_M_TYPE => '',
            'type' => 'str',
        ],
        'last_name' => [
            self::BITRIX_B_TYPE => 'UF_CRM_5D38356DB0BDB',
            self::BITRIX_M_TYPE => '',
            'type' => 'str',
        ],
        'iin' => [
            self::BITRIX_B_TYPE => 'UF_CRM_5C502158B09B7',
            self::BITRIX_M_TYPE => '',
            'type' => 'int',
        ],
        'phone' => [
            self::BITRIX_B_TYPE => '',
            self::BITRIX_M_TYPE => '',
            'type' => 'int',
        ],
        'email' => [
            self::BITRIX_B_TYPE => '',
            self::BITRIX_M_TYPE => '',
            'type' => 'email',
        ],
    ];

    protected const REQUEST_MAP_LOCAL = [
        'first_name' => [
            self::BITRIX_B_TYPE => 'UF_CRM_1619527771578',
            self::BITRIX_M_TYPE => '',
            'type' => 'str',
        ],
        'last_name' => [
            self::BITRIX_B_TYPE => 'UF_CRM_5D38356DB0BDB',
            self::BITRIX_M_TYPE => '',
            'type' => 'str',
        ],
        'second_name' => [
            self::BITRIX_B_TYPE => 'UF_CRM_5D38362365A13',
            self::BITRIX_M_TYPE => '',
            'type' => 'str',
        ],
        'latin_first_name' => [
            self::BITRIX_B_TYPE => 'UF_CRM_1619527843599',
            self::BITRIX_M_TYPE => '',
            'type' => 'str',
        ],
        'latin_last_name' => [
            self::BITRIX_B_TYPE => 'UF_CRM_1619527862877',
            self::BITRIX_M_TYPE => '',
            'type' => 'str',
        ],
        'birth_date' => [
            self::BITRIX_B_TYPE => 'UF_CRM_1619527910722',
            self::BITRIX_M_TYPE => '',
            'type' => 'date',
        ],
        'iin' => [
            self::BITRIX_B_TYPE => 'UF_CRM_5C502158B09B7',
            self::BITRIX_M_TYPE => '',
            'type' => 'int',
        ],
        'issuer' => [
            self::BITRIX_B_TYPE => 'UF_CRM_1619527968847',
            self::BITRIX_M_TYPE => '',
            'type' => 'str',
        ],
        'valid_from' => [
            self::BITRIX_B_TYPE => 'UF_CRM_1619527980976',
            self::BITRIX_M_TYPE => '',
            'type' => 'date',
        ],
        'valid_to' => [
            self::BITRIX_B_TYPE => 'UF_CRM_1619527999054',
            self::BITRIX_M_TYPE => '',
            'type' => 'date',
        ],
        'doc_num' => [
            self::BITRIX_B_TYPE => 'UF_CRM_1619528008772',
            self::BITRIX_M_TYPE => '',
            'type' => 'int',
        ],
        'gender' => [
            self::BITRIX_B_TYPE => 'UF_CRM_1619528018107',
            self::BITRIX_M_TYPE => '',
            'type' => 'str',
        ],
        'birth_place' => [
            self::BITRIX_B_TYPE => 'UF_CRM_1619528027995',
            self::BITRIX_M_TYPE => '',
            'type' => 'str',
        ],
        'nation' => [
            self::BITRIX_B_TYPE => 'UF_CRM_1619528041291',
            self::BITRIX_M_TYPE => '',
            'type' => 'str',
        ],
        'citizen' => [
            self::BITRIX_B_TYPE => 'UF_CRM_1619528048890',
            self::BITRIX_M_TYPE => '',
            'type' => 'str',
        ],
    ];

    protected const BARCODE_MAP = [
        self::BITRIX_B_TYPE => 'UF_CRM_1621077173967',
        self::BITRIX_M_TYPE => '',
    ];

    protected string $currentType = self::BITRIX_B_TYPE;
    protected int $responseCode = 200;
    protected array $data = [];
    protected array $response = [];
    protected array $requestData = [];
    protected Mysql $mysql;
    protected int $limit;

    public function __construct(array $data, Mysql $mysql, int $limit)
    {
        $type = $data['type'];
        unset($data['type']);
        $this->mysql = $mysql;
        $this->currentType = self::TYPE_MAP[$type];
        $this->mysql->setTableType($type);

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
            http_response_code(400);
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
                $this->setStatusCode(400)
                    ->setResponse(
                        [
                            'status' => 'fail',
                            'message' => 'Invalid data'
                        ]
                    )
                    ->send();
            }

            $response = $this->curlToBitrix();

            $this->setResponse($response)->setStatusCode(201)->send();
        } catch (\Throwable $e) {
            $this->setResponse(
                [
                    'status' => 'error',
                    'message' => 'Что-то пошло не так, попробуйте позже',
                ]
            )->setStatusCode(418)->send();
        }
    }

    protected function checkLimit(): bool
    {
        return $this->mysql->totalCount() >= $this->limit;
    }

    protected function send(): void
    {
        http_response_code($this->responseCode);
        echo json_encode($this->response);
        exit(1);
    }

    protected function setResponse(array $data): self
    {
        $this->response = $data;

        return $this;
    }

    protected function validDataAndGenerateRequest(): bool
    {
        $result = true;

        if (empty($this->data['form_type'])) {
            $result = false;
        }

        if ($this->data['form_type'] === 'online') {
            $map = self::REQUEST_MAP_ONLINE;
        } else {
            $map = self::REQUEST_MAP_LOCAL;
        }

        foreach ($map as $key => $rules) {
            if (empty($this->data[$key])) {
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
                default:
                    if (isset($this->requestData[$rules[$this->currentType]])) {
                        $this->requestData[$rules[$this->currentType]] = (string)$this->data[$key];
                    }
                    break;
            }
        }

        if (!empty($this->mysql->selectByIin((int)$this->data['iin']))) {
            $this->setResponse(
                [
                    'status' => 'duplicate',
                    'message' => 'Ваша заявка уже была отправлена ранее'
                ]
            )->setStatusCode(208)->send();
            $result = 0;
        }

        if ($result) {
            $this->requestData['TITLE'] = $this->data['first_name'] . ' ' . $this->data['last_name'];
            $this->requestData['CATEGORY_ID'] = self::CATEGORY_MAP[$this->currentType];
            $this->requestData[self::BARCODE_MAP[$this->currentType]] = $this->genBarCode(
                $this->mysql->createAbiturient(
                    (int)$this->data['iin'],
                    (string)$this->data['first_name'],
                    (string)$this->data['last_name']
                )
            );
        }

        return $result;
    }

    protected function validateDate($date, $format = 'd.m.Y'): bool
    {
        $d = DateTime::createFromFormat($format, $date);

        return $d && $d->format($format) === $date;
    }

    protected function setStatusCode(int $code): self
    {
        $this->responseCode = $code;
        return $this;
    }

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

        if (empty($result['result']) && $info['http_code'] >= 300) {
            $response = ['message' => 'Произошла ошибка при отправке, обновите страницу и повторите попытку'];
        } else {
            $response = ['message' => 'Заявка успешно создана с номером ' . $result['result']];
        }

        return $response;
    }
}