<?php

use function DI\env;
use function DI\factory;
use function DI\get;
use function DI\object;
use GuzzleHttp\Client;
use League\Csv\Reader;
use Psr\Container\ContainerInterface;
use SchoolData\Command\ImportCommand;
use SchoolData\Command\IndexCommand;
use Symfony\Component\Console\Application;

return [

    Application::class => object()
        ->method('addCommands', get('commands')),

    'commands' => [
        factory([ImportCommand::class, 'create']),
        factory([IndexCommand::class, 'create']),
    ],

    'csv.reader' => function (ContainerInterface $container) {
        /** @var $csv \League\Csv\Reader */
        $csv = Reader::createFromPath(getenv('DATA_FILE'));
        $csv->setHeaderOffset(0);
        return $csv;
    },

    'es.config.filename' => env('ES_CONFIG_FILENAME'),

    'http.client' => object(Client::class)
        ->constructorParameter('config', [
            'base_uri' => getenv('ES_URL'),
            'auth' => [
                getenv('ES_USER'),
                getenv('ES_PASS')
            ],
        ]),
];
