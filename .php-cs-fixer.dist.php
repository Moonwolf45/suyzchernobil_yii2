<?php

$finder = PhpCsFixer\Finder::create()
    ->in([
        __DIR__ . '/behaviors',
        __DIR__ . '/commands',
        __DIR__ . '/components',
        __DIR__ . '/controllers',
        __DIR__ . '/jobs',
        __DIR__ . '/models',
        __DIR__ . '/modules',
        __DIR__ . '/widgets',
    ])
    ->exclude([
        'runtime',
        'web/assets',
    ])
    ->name('*.php');

$config = new PhpCsFixer\Config();

return $config->setRules([
    '@PSR12' => true,                               // Базовый стандарт PSR-12
    'array_syntax' => ['syntax' => 'short'],        // Только короткие массивы []
    'no_unused_imports' => true,                    // Удалять неиспользуемые use
    'ordered_imports' => ['sort_algorithm' => 'alpha'], // Сортировка use по алфавиту
//    'strict_param' => true,                         // Строгие типы в функциях
    'cast_spaces' => ['space' => 'single'],         // Пробел после (int), (string)
    'concat_space' => ['spacing' => 'one'],         // Пробелы вокруг точки при конкатенации
//    'declare_strict_types' => true,                 // Автоматически добавлять declare(strict_types=1);
])->setRiskyAllowed(false) // Запрещаем ломающие код изменения
->setFinder($finder);