# Реализация наивного байесовского классификатора на PHP
Служит для классификации текстов по известным рубрикам на основании
обучающей выборки

### Пример
```php
$trainer = new Trainer();

foreach ($trainData as $document) {
    $trainer->addDocument($document['text'], $document['class']);
}
$model = $trainer->getModel();

$classifier = new Classifier($model);
foreach ($predictionData as $document) {
    $result = $classifier->classifyReturningPercent($document['text']);
    $mostProbableClass = array_keys($result)[0];
}
```
в `$result` будет храниться ассоциативный массив
вероятных классов документа и их вероятности:
```
[
   'class1' => 0.95,
   'class2' => 0.05
]
```

### Добавление значящих св-в документа
Пример добавления свойства "автор документа"
в ветке feature-add-author-property