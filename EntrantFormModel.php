<?php

class EntrantFormModel
{
    protected const REL_MAP = [
        'css' => 'stylesheet',
        'ico' => 'shortcut icon',
        'default' => 'alternate',
    ];
    protected string $title = '';
    protected array $headsTag = [
        'meta' => ['need_close' => false],
        'link' => ['need_close' => false],
        'script' => ['need_close' => true],
    ];

    /**
     * @return self
     */
    public static function make(): self
    {
        return new self;
    }

    /**
     * Возвращает только строку Title, тэг нужно поставить вручную
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return self
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Добавляет meta тэг с атрибутами
     * @param array $attrs
     * @return $this
     */
    public function addHeadMetaTag(array $attrs): self
    {
        $this->headsTag['meta'][] = $attrs;

        return $this;
    }

    /**
     * Добавляет script тэг
     * @param string $src
     * @param array $attrs
     * @return $this
     */
    public function addHeadScriptTag(string $src, array $attrs = []): self
    {
        if ($this->validLink($src)) {
            $this->headsTag['script'][] = ['src' => $src] + $attrs;
        }

        return $this;
    }

    /**
     * Проверяем символические ссылки, прямой путь к файлу или url адрес
     * @param string $link
     * @return bool
     */
    protected function validLink(string $link): bool
    {
        // Проверим символическую ссылку изначально, так как это в любом случае читабельный файл
        // Невалидный url вернёт false, поэтому проверим на empty
        // Нам не интересна сама ссылка, если она возвращает невалидную информацию то это уже не наша проблема
        return is_link($link) || (is_file($link) && is_readable($link)) || !empty(parse_url($link));
    }

    /**
     * Добавляет link тэг
     * @param string $href
     * @param array $attrs
     * @return $this
     */
    public function addHeadLinkTag(string $href, array $attrs = []): self
    {
        if ($this->validLink($href)) {
            $type = $this->getFileType($href);

            $this->headsTag['link'][] = [
                    'rel' => $attrs['rel'] ?? self::REL_MAP[$type] ?? self::REL_MAP['default'],
                    'href' => $href,
                ] + $attrs;
        }

        return $this;
    }

    /**
     * Возвращает тип файла
     * @param string $str
     * @return string|null
     */
    protected function getFileType(string $str): ?string
    {
        $result = null;

        if (is_link($str)) {
            $str = realpath($str);
        }

        $arr = explode('.', $str); // filetype() не подойдёт потому что может прийти url или симлинк

        if ($arr) {
            $result = array_pop($arr);
        }

        return $result;
    }

    /**
     * Выводит head тэги
     */
    public function loadHeadTags(): void
    {
        foreach ($this->headsTag as $tag => $data) {
            $needClose = $data['need_close'] ?? true;
            unset($data['need_close']);

            if (!empty($data)) {
                foreach ($data as $attrs) {
                    echo '<' . $tag; // Открываем тэг

                    foreach ($attrs as $attr => $value) {
                        echo ' ' . $attr . '=\'' . $value . '\'';
                    }

                    echo '>'; // Не забываем закрыть тэг

                    if ($needClose) { // Если нужен закрывающий тэг, поставим его
                        echo '</' . $tag . '>';
                    }

                    echo PHP_EOL; // Сделаем перенос после каждого тега
                }
            }
        }
    }
}