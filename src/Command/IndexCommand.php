<?php

namespace SchoolData\Command;

use GuzzleHttp\Exception\ClientException;
use function GuzzleHttp\json_decode;
use GuzzleHttp\Client;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class IndexCommand extends Command
{
    public static function create(ContainerInterface $container) {
        return new static(
          $container->get('http.client'),
          $container->get('es.config.filename'));
    }

    /** @var \GuzzleHttp\Client */
    protected $client;

    /** @var string */
    protected $configFilename;

    public function __construct(Client $client, string $configFilename)
    {
        $this->client = $client;
        $this->configFilename = $configFilename;
        parent::__construct();
    }


    protected function configure()
    {
        $this->setName('index');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $output->writeln("Deleting existing index...");
            $this->client->delete('/schools');
            $output->writeln("Done");
        }
        catch (ClientException $exception) {
            if ($exception->getResponse()->getStatusCode() == '404') {
                $output->writeln("Index not found");
            }
            else throw $exception;
        }

        $output->writeln("Putting new configuration...");
        $this->client->put('/schools', [
            'json' => json_decode(file_get_contents($this->configFilename)),
        ]);
        $output->writeln("Done");
    }

}
