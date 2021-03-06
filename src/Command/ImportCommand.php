<?php

namespace SchoolData\Command;

use GuzzleHttp\Client;
use Interop\Container\ContainerInterface;
use League\Csv\Reader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ImportCommand extends Command
{
    public static function create(ContainerInterface $container) {
        return new static(
            $container->get('csv.reader'),
            $container->get('http.client'));
    }

    /** @var \League\Csv\Reader */
    protected $csv;

    /** @var \GuzzleHttp\Client */
    protected $client;

    public function __construct(Reader $csv, Client $client)
    {
        $this->csv = $csv;
        $this->client = $client;
        parent::__construct();
    }


    protected function configure()
    {
        $this->setName('import');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $records = $this->csv->getRecords();

        $type = getenv('DATA_FILE_TYPE');

        if (!in_array($type, ['uk', 'scot', 'ie', 'ni'])) {
            throw new \Exception("Invalid data type must be uk, scot, ni, or ie.");
        }

        $progress = new ProgressBar($output, $this->csv->count());
        $progress->start();

//        $data = [];

//        $groupedFields = [
//            'LA (name)',
//            'LA (code)',
//            'TypeOfEstablishment (name)',
//            'EstablishmentTypeGroup (name)',
//            'EstablishmentStatus (name)',
//            'PhaseOfEducation (name)',
//        ];

        foreach ($records as $offset => $record) {
//            foreach ($groupedFields as $field) {
//                $data[$field][$record[$field]] = true;
//            }

            $id = FALSE;

            switch ($type) {
                case 'scot':
                    if ($record['SeedCode']) {
                        $id = 'seed--' . $record['SeedCode'];
                        $esData = [
                            'name' => trim($record['School Name']),
                            'la_name' => trim($record['LA Name']),
                        ];
                    }
                    break;

                case 'ni':
                    if ($record['Reference']) {
                        $id = 'ref--' . $record['Reference'];
                        $esData = [
                            'name' => trim($record['Institution_Name']),
                            'la_name' => trim($record['County_Name']),
                        ];
                    }
                    break;

                case 'ie':
                    if ($record['Roll Number']) {
                        $id = 'roll--' . $record['Roll Number'];
                        $esData = [
                            'name' => trim($record['Official Name']),
                            'la_name' => trim($record['Local Authority Description']),
                        ];
                    }
                    break;

                case 'uk':
                    if ($record['URN']) {
                        $id = 'urn--' . $record['URN'];
                        $esData = [
                            'urn' => $record['URN'] ?? '',
                            'name' => utf8_encode($record['EstablishmentName']),
                            'la_name' => $record['LA (name)'],
                        ];
                    }
                    break;

                default:
            }

            if ($id !== FALSE) {
                try {
                    $this->client->post('/schools/school/'.$id, [
                        'json' => $esData
                    ]);
                }
                catch (\InvalidArgumentException $exception) {
                    var_dump($record);
                    throw $exception;
                }
            }


            $progress->advance();
        }
        $progress->finish();
        $output->writeln("\nDone.");

//        foreach ($groupedFields as $field) {
//            $data[$field] = array_keys($data[$field]);
//        }
//        var_dump($data);
//        var_dump($this->csv->getHeader());
    }

}
