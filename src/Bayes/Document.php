<?php

namespace Sparfenov\Classification\Bayes;

use NXP\Stemmer;
use Sparfenov\Classification\Helpers\StopWords;

/**
 * Классифицируемый документ
 */
class Document
{
    /**
     * @var string
     */
    private $class;

    /**
     * @var string
     */
    private $text;

    /**
     * @var string[]
     */
    private $normalizedWords = null;

    /**
     * Модель классифицируемого документа
     *
     * @param string $text - текст, содержание
     * @param string $class - класс документа если известен
     */
    public function __construct(string $text, ?string $class = null)
    {
        $this->class = $class;
        $this->text = $text;
    }

    /**
     * Возвращает массив нормальных форм слов в документе
     *
     * @return string[]
     */
    public function getNormalizedWords()
    {
        if ($this->normalizedWords !== null) {
            return $this->normalizedWords;
        }

        $stemmer = new Stemmer();
        $stopWords = StopWords::getStopWordsRU();

        $text = $this->getText();
        // вычищаем текст от лишних символов
        $text = preg_replace('#[:"\'’«»?]#u', '', $text);
        $text = preg_replace("#[^А-Яа-яA-Za-z0-9]#u", ' ', $text);

        // разбиваем на слова по пробелам
        $words = preg_split("#\s#", $text);

        // убираем стоп-слова
        $words = array_diff($words, $stopWords);

        // приводим слова к нормальной форме
        $words = array_map(function ($word) use ($stemmer) {
            return $stemmer->getWordBase(mb_strtolower($word));
        }, $words);

        $this->normalizedWords = array_filter($words);

        return $this->normalizedWords;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }
}
