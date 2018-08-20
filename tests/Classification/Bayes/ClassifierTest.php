<?php

namespace Sparfenov\Classification\Tests\Bayes;

use PHPUnit\Framework\TestCase;
use Sparfenov\Classification\Bayes\Classifier;
use Sparfenov\Classification\Bayes\Model;
use Sparfenov\Classification\Bayes\Trainer;

class ClassifierTest extends TestCase
{
    /**
     * @param array $trainData
     * @param array $predictionData
     */
    public function testClassification()
    {
        $handle = fopen('test_data_authors.csv', 'r');
        $trainer = new Trainer();

        fwrite(STDERR, print_r("TRAINING\n", true));
        $i = 0;
        while ($i < 15000) {
            $i++;
            fwrite(STDERR, print_r("$i\n", true));
            $document = fgetcsv($handle);
            $trainer->addDocument($document[2], $document[1], $document[0]);
        }
        $model = $trainer->getModel();

        fwrite(STDERR, print_r("TESTING\n", true));

        $classifier = new Classifier($model);
        while ($i < 20000) {
            $i++;
            fwrite(STDERR, print_r("$i\n", true));
            $document = fgetcsv($handle);
            $result = $classifier->classify($document[2], $document[1]);
            $mostProbableClass = array_keys($result)[0];
            $predictStats[] = (int)($mostProbableClass === $document[0]);
        }
        fclose($handle);

        $accuracy = array_sum($predictStats) / count($predictStats);
        var_dump($predictStats, $accuracy);
        $this->assertGreaterThan(0.68, $accuracy);
    }

    public function testSerialize()
    {
        $trainer = new Trainer();
        $trainer->addDocument('some text', 'author', 'some_class');
        $trainer->addDocument('some other text', 'author2', 'class');

        $model = $trainer->getModel();

        $this->assertEquals(Model::createFromJson((string)$model), $model);
    }
}
