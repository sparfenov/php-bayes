<?php

namespace Sparfenov\Classification\Bayes;

/**
 * Классификатор
 */
class Classifier
{
    /**
     * модель классификатора (набор преднасчитанных значений на обучающей выборке)
     *
     * @var Model
     */
    private $model;

    /**
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Определяем к какому классу относится текст
     * возвращает результат в относительных логарифмических оценках (чем ближе к 0 тем лучше)
     *
     * @param string $text
     * @return []float
     */
    public function classify(string $text) : array
    {
        $logProbabilities = [];
        foreach ($this->model->getClasses() as $class) {
            $logProbabilities[$class] = $this->calcLogProbability($text, $class);
        }

        arsort($logProbabilities);
        return $logProbabilities;
    }

    /**
     * То же что и функция выше, только пересчитывает результат в процентах
     *
     * @param string $text
     * @return array
     */
    public function classifyReturningPercent(string $text) : array
    {
        $logs = $this->classify($text);

        // Вычисляем вероятность в процентах из логарифмических оценок
        $probabilities = [];
        foreach ($logs as $class => $prob) {
            $probabilities[$class] = exp($prob) / (array_sum(array_map(function ($prob) {
                return exp($prob);
            }, $logs)));
        }

        arsort($probabilities);
        return $probabilities;
    }

    /**
     * Вычисляем вероятность принадлежности строки к классу
     *
     * @param string $text
     * @param string $class
     * @return float
     */
    public function calcLogProbability(string $text, string $class) : float
    {
        $document = new Document($text);
        $wordProb = 0.0;

        foreach ($document->getNormalizedWords() as $word) {
            $wordProb += $this->model->getLogWordProbability($class, $word);
        }

        return $this->model->getLogClassProbability($class) + $wordProb;
    }
}
