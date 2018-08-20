<?php

namespace Sparfenov\Classification\Tests\Bayes;

use PHPUnit\Framework\TestCase;
use Sparfenov\Classification\Bayes\Classifier;
use Sparfenov\Classification\Bayes\Model;
use Sparfenov\Classification\Bayes\Trainer;

class ClassifierTest extends TestCase
{
    /**
     * @dataProvider sampleDataProvider
     * @param array $trainData
     * @param array $predictionData
     */
    public function testClassification($trainData, $predictionData)
    {
        $trainer = new Trainer();

        foreach ($trainData as $document) {
            $trainer->addDocument($document['text'], $document['class']);
        }
        $model = $trainer->getModel();

        $classifier = new Classifier($model);
        foreach ($predictionData as $document) {
            $result = $classifier->classify($document['text']);
            $mostProbableClass = array_keys($result)[0];
            $this->assertEquals($document['class'], $mostProbableClass);
        }
    }

    public function sampleDataProvider()
    {
        return [
            [
                'train' => [
                    ['class' => 'Звезды - Новости', 'text' => 'Эмили Ратаковски на прогулке с мужем'],
                    ['class' => 'Звезды - Новости', 'text' => 'Брэд Питт выиграл «первый раунд» суда у Анджелины Джоли'],

                    ['class' => 'Стиль жизни', 'text' => 'Главные по театру: 8 ключевых российских режиссеров'],
                    ['class' => 'Стиль жизни', 'text' => 'Что смотреть на театральных подмостках Нью-Йорка?'],
                ],
                'prediction' => [
                    ['class' => 'Звезды - Новости', 'text' => 'Бред Питт в Нью-Йорке на прогулке с мужем'],
                    ['class' => 'Стиль жизни', 'text' => 'Что посмотреть на выходных'],
                ],
            ],
        ];
    }

    public function testSerialize()
    {
        $trainer = new Trainer();
        $trainer->addDocument('some text', 'some_class');
        $trainer->addDocument('some other text', 'class');

        $model = $trainer->getModel();

        $this->assertEquals(Model::createFromJson((string)$model), $model);
    }
}
