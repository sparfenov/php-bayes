<?php

namespace Sparfenov\Classification\Bayes;

/**
 * Подсчет данных для модели классификатора на основании документов обучающей выборки
 */
class Trainer
{
    /**
     * Массив документов на которых обучаемся
     *
     * @var Document[]
     */
    private $documents = [];

    /**
     * Добавляем документ для тренировки
     *
     * @param string $text
     * @param string $author
     * @param string $class
     */
    public function addDocument(string $text, string $author, string $class)
    {
        $this->documents[] = new Document($text, $author, $class);
    }

    /**
     * Возвращает модель классификатора
     * Набор данных по которым можно рассчитать вероятность принадлежности документа к одному из классов
     *
     * @return Model
     */
    public function getModel() : Model
    {
        $documentsByClasses = [];
        $wordsCountByClasses = [];
        $classesLength = [];
        $words = [];
        $authorsByClasses = [];

        foreach ($this->documents as $doc) {
            $docWords = $doc->getNormalizedWords();
            $classesLength[$doc->getClass()] = ($classesLength[$doc->getClass()] ?? 0) + count($docWords);
            $documentsByClasses[$doc->getClass()] = ($documentsByClasses[$doc->getClass()] ?? 0) + 1;

            foreach ($docWords as $word) {
                $wordsCountByClasses[$doc->getClass()][$word] = ($wordsCountByClasses[$doc->getClass()][$word] ?? 0) + 1;
            }

            $authorsByClasses[$doc->getAuthor()][$doc->getClass()]
                = ($authorsByClasses[$doc->getAuthor()][$doc->getClass()] ?? 0) + 1;

            $words = array_merge($words, $docWords);
        }

        return new Model(
            count(array_unique($words)),
            $wordsCountByClasses,
            $classesLength,
            $documentsByClasses,
            $authorsByClasses
        );
    }
}
