<?php

namespace Sparfenov\Classification\Bayes;

/**
 * Модель классификатора
 */
class Model
{
    /**
     * Всего уникальных слов в документах обучающей выборки
     *
     * @var int
     */
    private $dictionarySize;

    /**
     * Статистика кол-ва слов по категориям
     * map[string $class][string $word, int $count]
     *
     * @var array
     */
    private $wordCountsByClasses;

    /**
     * Кол-во слов по классам включая повторы
     * map[string $class][int $wordCount]
     *
     * @var [string][int]
     */
    private $classesLength;

    /**
     * Кол-во документов по классам
     * map[string $class][int $docsCount]
     *
     * @var array
     */
    private $documentsByClasses;

    /**
     * @var
     */
    private $authorsByClasses;

    /**
     *
     * @param int $dictionarySize - кол-во уникальных слов всего
     * @param array $documentsByClasses - кол-во документов в классах
     * @param array $classesLength - кол-во слов в классах
     * @param array $wordCountsByClasses - кол-во каждого из слов в каждом из классов
     * @param array authorsByClasses - кол-во документов каждого автора в каждом из классов
     */
    public function __construct(
        int $dictionarySize,
        array $wordCountsByClasses,
        array $classesLength,
        array $documentsByClasses,
        array $authorsByClasses
    ) {
        $this->dictionarySize = $dictionarySize;
        $this->wordCountsByClasses = $wordCountsByClasses;
        $this->classesLength = $classesLength;
        $this->documentsByClasses = $documentsByClasses;
        $this->authorsByClasses = $authorsByClasses;
    }

    /**
     * @param string $modelAsJson
     * @return Model
     */
    public static function createFromJson(string $modelAsJson) : Model
    {
        $model = json_decode($modelAsJson, true);

        return new self(
            $model['dictionarySize'],
            $model['wordCountsByClasses'],
            $model['classesLength'],
            $model['documentsByClasses'],
            $model['authorsByClasses']
        );
    }

    /**
     * Вычисляем логарифм оценки вероятности слова в классе
     *
     * @param string $class
     * @param string $word
     * @return float
     */
    public function getLogWordProbability(string $class, string $word) : float
    {
        return log(
            (($this->wordCountsByClasses[$class][$word] ?? 0) + 1)
            /
            ($this->classesLength[$class] + $this->dictionarySize)
        );
    }

    /**
     * Логарифм вероятности класса
     *
     * @param string $class
     * @return float
     */
    public function getLogClassProbability(string $class) : float
    {
        return log(
            $this->documentsByClasses[$class] / array_sum($this->documentsByClasses)
        );
    }

    /**
     * Логарифм вероятности автора в классе
     *
     * @param string $class
     * @param string $author
     * @return float
     */
    public function getLogAuthorProbability(string $class, string $author) : float
    {
        return log(
            (($this->authorsByClasses[$author][$class] ?? 0) + 1)
            /
            (array_sum($this->authorsByClasses[$author] ?? []) + 1)
        );
    }

    /**
     * @return array
     */
    public function getClasses() : array
    {
        return array_keys($this->documentsByClasses);
    }

    /**
     * Сериализация модели для возможности сохранения ее состояния в _любое_ хранилище,
     * чтобы при необходимости классифицировать текст, не пересчитывать модель заново
     *
     * @return array
     */
    public function getAuthors() : array
    {
        return array_keys($this->authorsByClasses);
    }

    /**
     * @return string
     */
    public function __toString() : string
    {
        return json_encode(
            [
                'dictionarySize' => $this->dictionarySize,
                'wordCountsByClasses' => $this->wordCountsByClasses,
                'classesLength' => $this->classesLength,
                'documentsByClasses' => $this->documentsByClasses,
                'authorsByClasses' => $this->authorsByClasses,
            ]
        ,JSON_UNESCAPED_UNICODE);
    }
}
