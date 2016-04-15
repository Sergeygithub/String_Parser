<?php

/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 12.04.2016
 * Time: 14:19
 */
class Parser
{
    /**
     * открывающий символ
     */
    const OPEN_SYMBOL = '{';

    /**
     * закрывающий символ
     */
    const CLOSE_SYMBOL = '}';

    /**
     * символ "ИЛИ"
     */
    const COMPARISON_SYMBOL = '|';

    /**
     * Строка, которую будем парсить
     * @var null|string
     */
    private $string = null;

    /**
     * Длина строки
     * @var int|null
     */
    private $length = null;

    /**
     * массив с текущим значением подстроки
     * @var array
     */
    private $current_array = array();

    /**
     * временный массив
     * @var array
     */
    private $temp_array = array();

    /**
     * Массив с результатом
     * @var null
     */
    private $result_array = null;

    /**
     * Позиция начала строки
     * @var null
     */
    private $position = 0;

    /**
     * Текущая позициця в строке
     * @var null
     */
    private $current_position = null;

    /**
     * Parser constructor.
     * @param string $string
     * @throws Exception wrong string
     */
    public function __construct($string)
    {
        if (is_string($string)) {
            // количество открывающих символов
            $openSymbolCount = substr_count($string, self::OPEN_SYMBOL);
            // количество закрывающих символов
            $closeSymbolCount = substr_count($string, self::CLOSE_SYMBOL);
            // если количество открывающых и закрывающих символов разное, то строка невалидная
            if ($openSymbolCount !== $closeSymbolCount) {
                throw new Exception('Wrong string');
            }
            $this->string = $string;
            $this->length = strlen($string);
        }
    }

    /**
     * парсим строку
     */
    public function parse()
    {
        for ($this->position; $this->position <= $this->length; $this->position++) {
            switch ($this->string[$this->position]) {
                case self::OPEN_SYMBOL:
                    // если встречаем открывающий символ, то всю подстроку, что была до него,
                    // записываем в текущий массив
                    $this->writeToArray();
                    // добавляем текущий массив во временный и сбрасываем текущий массив
                    // так как строка внутри {} также может содержать строку {}
                    array_push($this->temp_array, $this->current_array);
                    $this->current_array = array();
                    break;
                case self::CLOSE_SYMBOL:
                    // если встречаем закрывающий символ, то всю подстроку от момента {
                    // записываем в текущий массив
                    $this->writeToArray();
                    $temp = $this->current_array;
                    // извлекаем последний элемент из временного массива
                    // и записываем в текущий
                    $this->current_array = array_pop($this->temp_array);
                    $this->current_array[] = $temp;
                    break;
                default:
                    if ($this->current_position === null) {
                        $this->current_position = $this->position;
                    }
            }
            if ($this->position === $this->length) {
                // если достигли конца строки, то всю подсроку от последнего }
                // записываем в текущий массив
                $this->writeToArray();
            }
        }
        $this->result_array = $this->current_array;
    }

    /**
     * Записывает подстроку в текщий массив и сбрасывает значение current_position
     */
    private function writeToArray()
    {
        if ($this->current_position !== null) {
            $string = substr($this->string, $this->current_position, $this->position - $this->current_position);
            $this->current_position = null;
            $this->current_array[] = $string;
        }
    }

    /**
     * Возвращает распарсеную строку
     * @return null|string
     */
    public function getResultString()
    {
        if (empty($this->result_array)) {
            return null;
        }

        return $this->resultToString($this->result_array);
    }

    /**
     * Формирует из массива строку, разбивая строки вида string1|string2
     * выбирая одно значение, либо string1 либо string2
     * количество таких подстрок неограничено, string1|string2|string3.... и т.д.
     * подстроки также могут содержать строки вида string1|string2
     * @param $array
     * @return string
     */
    private function resultToString($array)
    {
        $result = '';

        foreach ($array as $item) {
            // если $item это массив, значит эта строка в этом массиве находилась между {}
            // и ее нужно обработать
            if (is_array($item)) {
                $result .= $this->resultToString($item);
            }
            if (is_string($item)) {
                // разбиваем строку символом "ИЛИ" и выбираем случайную часть
                $res_array = explode(self::COMPARISON_SYMBOL, $item);
                $result .= $res_array[array_rand($res_array)];
            }
        }

        return $result;
    }
}