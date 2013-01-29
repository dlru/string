<?php
/**
 * DesignLab Framework
 *
 * Copyright (c) 2001-2013, DesignLab, LLC. All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *  -   Redistributions of source code must retain the above copyright
 *      notice, this list of conditions and the following disclaimer.
 *
 *  -   Redistributions in binary form must reproduce the above copyright
 *      notice, this list of conditions and the following disclaimer in
 *      the documentation and/or other materials provided with the
 *      distribution.
 *
 *  -   Neither the name of the DesignLab, LLC nor the names of its
 *      contributors may be used to endorse or promote products derived
 *      from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @copyright   2001-2013 DesignLab, LLC.
 * @author      Victor Yasinovsky <victor@designlab.ru>
 * @license     http://opensource.org/licenses/BSD-3-Clause
 * @link        http://www.designlab.ru
 */



namespace DL\Main;

/**
 * Работа со строкой
 *
 * @package DL_Core
 * @subpackage Main
 * @author Victor Yasinovsky
 */
class String implements \Iterator, \ArrayAccess {



    /**
     * Строка
     *
     * @var string
     */
    private $_string = null;

    /**
     * Кодировка
     *
     * @var string
     */
    private $_encoding = null;



    /**
     * Длинна строки
     *
     * @var int
     */
    private $_length = null;

    /**
     * Размер строки в байтах
     *
     * @var int
     */
    private $_size = null;



    /**
     * Конструктор
     *
     * @param string $string Строка
     * @param string $encoding Кодировка
     * @throws String\Exception
     */
    public function __construct($string, $encoding='UTF-8') {
        static $mb_extension_checked = false;
        if ($mb_extension_checked == false) {
            if (!extension_loaded('mbstring')) {
                throw new String\Exception( // Расширение не найдено ...
                    'Multibyte string functions extension was not loaded'
                );
            }
            // Все хорошо - едем дальше!
            $mb_extension_checked = true;
        }
        $this->_string = strval($string);
        $this->_encoding = $encoding;
    }



    /**
     * Возвращает строковое представление объекта
     *
     * @return string
     */
    public function __toString() {
        return $this->_string;
    }



    /**
     * Возвращает содержимое строки
     *
     * @return string
     */
    public function value() {
        return $this->_string;
    }



    /**
     * Возвращает кодировку строки
     *
     * @return string
     */
    public function encoding() {
        return $this->_encoding;
    }



    /**
     * Преобразует кодировку символов
     *
     * @param string $encoding Кодировка
     * @return \DL\Main\String
     * @throws String\Exception
     */
    public function convert($encoding) {
        $this->_string = @mb_convert_encoding($this->_string, $encoding, $this->_encoding);
        if ($this->_string === false) { // Конвертировать не получилось - красиво ответим почему
            throw new String\Exception('Can not convert encoding to "' . strval($encoding) . '"');
        }
        $this->_encoding = $encoding;
        $this->_size = null;
        return $this;
    }



    /**
     * Возвращает длинну строки в символах
     *
     * @return int
     */
    public function length() {
        if (is_null($this->_length)) { // Эффективно и единожды считаем
            $this->_length = mb_strlen($this->_string, $this->_encoding);
        }
        return $this->_length;
    }



    /**
     * Возвращает размер строки в байтах
     *
     * @return int
     */
    public function size() {
        if (is_null($this->_size)) { // Эффективно и единожды считаем
            $this->_size = mb_strlen($this->_string, '8bit');
        }
        return $this->_size;
    }



    /**
     * Возвращает часть строки
     *
     * @param int $start Позиция символа, с которой выделяется подстрока
     * @param int|null $length Максимальное количество символов возвращаемой подстроки
     * @return string
     */
    public function substr($start, $length=null) {
        return mb_substr(
            $this->_string, $start,
            // Длинна не передана - возвращаем до конца строки!
            is_null($length) ? $this->length() - $start : $length,
            $this->_encoding
        );
    }



    /**
     * Возвращает символ по индексу
     *
     * @param int $index Индекс
     * @return string
     */
    public function char($index) {
        return $this->substr($index, 1);
    }



    /**
     * Добавляет символ в конец
     *
     * @param string $char Символ
     * @return \DL\Main\String
     */
    public function append($char) {
        $this->length(); // Дернем, чтобы посчиталась длинна
        $this->_string .= $this->_cast_char($char); // Добавим
        $this->_length++; // Увеличим длинну строки на единичку
        $this->_size = null; // Не знаем сколько байт в символе
        return $this;
    }



    /**
     * Добавляет строку в конец
     *
     * @param string $string Строка
     * @throws String\Exception
     * @return \DL\Main\String
     */
    public function concat($string) {
        if (!is_string($string)) {
            throw new String\Exception('Given not a string');
        }
        $this->_string .= $string; // Добавим
        $this->_length = $this->_size = null;
        return $this;
    }



    /**
     * Возвращает строку, преобразованную в верхний регистр
     *
     * @return string
     */
    public function upper() {
        return mb_strtoupper($this->_string, $this->_encoding);
    }



    /**
     * Возвращает строку, преобразованную в нижний регистр
     *
     * @return string
     */
    public function lower() {
        return mb_strtolower($this->_string, $this->_encoding);
    }



    /**
     * Возвращает строку, в которой первый символ каждого слова преобразован в верхний регистр
     *
     * @return string
     */
    public function ucWords() {
        return mb_convert_case($this->_string, MB_CASE_TITLE, $this->_encoding);
    }



    /**
     * Возвращает строку, в которой первый символ преобразован в верхний регистр
     *
     * @return string
     */
    public function ucFirst() {
        $result = ''; // Результат пока пуст
        if ($this->length() > 0) { // Только если строка не пустая! :)
            $result .= mb_strtoupper($this->char(0), $this->_encoding);
            $result .= $this->substr(1);
        }
        return $result;
    }



    /**
     * Проверяет корректность и возвращает индекс
     *
     * @param int $index Индекс
     * @return int
     * @throws String\Exception
     */
    private function _cast_index($index) {
        if (is_numeric($index)) {
            $index = intval($index); // Приведем к числу
            if ($index >= 0 && $index < $this->length()) {
                return $index; // Вернем, если в интервале
            }
        }
        throw new String\Exception('Incorrect index');
    }



    /**
     * Проверяет корректность и возвращает символ
     *
     * @param string $char Символ
     * @return string
     * @throws String\Exception
     */
    private function _cast_char($char) {
        if (is_string($char) && mb_strlen($char, $this->_encoding) == 1) {
            return $char; // Вернем, если это строка из одного символа
        }
        throw new String\Exception('Incorrect character');
    }



    /**
     * Устанавливает символ по индексу
     *
     * @param int $index Индекс
     * @param string $char Символ
     * @throws String\Exception
     */
    private function _set_char($index, $char) {
        $char = $this->_cast_char($char);
        $index = $this->_cast_index($index);
        // Теперь можно составить строку из трех кусочков
        $this->_string = $this->substr(0, $index) . $char
            . $this->substr($index + 1, $this->length() - ($index + 1));
        // Мы не знаем какой символ был записан. А вдруг однобайтовый?
        $this->_size = null; // Сбросим размер строки на всякий случай
    }



    /**
     * Удаляет символ по индексу
     *
     * @param int $index Индекс
     */
    private function _unset_char($index) {
        $index = $this->_cast_index($index);
        $this->_string = $this->substr(0, $index)
            . $this->substr($index + 1, $this->length() - ($index + 1));
        $this->_length--; // Укоротим длинну, мы удалили один символ
        $this->_size = null; // Сбросим размер
    }



    /**
     * Реализация интерфейса "Iterator"
     * -----------------------------------------------------------------------------------------------------------------
     */



    /**
     * Текущая позиция
     *
     * @var int
     */
    private $_position = 0;



    /**
     * Возвращает итератор на первый элемент
     */
    function rewind() {
        $this->_position = 0;
    }



    /**
     * Возвращает текущий элемент
     *
     * @return string
     */
    function current() {
        return $this->char($this->_position);
    }



    /**
     * Возвращает ключ текущего элемента
     *
     * @return int
     */
    function key() {
        return $this->_position;
    }



    /**
     * Переходит к следующему элементу
     */
    function next() {
        ++$this->_position;
    }



    /**
     * Проверка корректности позиции
     *
     * @return bool
     */
    function valid() {
        return $this->_position < $this->length();
    }



    /**
     * Реализация интерфейса "ArrayAccess"
     * -----------------------------------------------------------------------------------------------------------------
     */



    /**
     * Определяет, существует ли заданное смещение
     *
     * @param int $offset Смещение
     * @return bool
     */
    public function offsetExists($offset) {
        return is_int($offset) &&
            ($offset >= 0 && $offset < $this->length());
    }



    /**
     * Возвращает заданное смещение
     *
     * @param int $offset Смещение
     * @return string
     */
    public function offsetGet($offset) {
        return $this->char($offset);
    }



    /**
     * Устанавливает заданное смещение
     *
     * @param int $offset Смещение
     * @param string $value Символ
     */
    public function offsetSet($offset, $value) {
        switch (is_null($offset)) {
            case true: // Добавим новый
                $this->append($value);
                break;
            case false: // Зададим значение старому
                $this->_set_char($offset, $value);
                break;
        }
    }



    /**
     * Удаляет смещение
     *
     * @param int $offset Смещение
     */
    public function offsetUnset($offset) {
        $this->_unset_char($offset);
    }



}