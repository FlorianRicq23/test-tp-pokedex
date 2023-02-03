<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Service\PokeClient; 

#[AsCommand(
    name: 'app:export-csv',
    description: 'Add a short description for your command',
)]
class ExportDataCommand extends Command
{
    /** @var PokeClient */
    protected $pokeClient;

    /**
     * RunCommand constructor.
     * @param PokeClient $pokeClient
     */
    public function __construct(PokeClient $pokeClient)
    {
        $this->pokeClient = $pokeClient;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Exports data to a CSV file.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = 'pokedex.csv';

        $application = $this->getApplication();
        $container = $application->getKernel()->getContainer();
        $generation = $container->getParameter('POKEMON_GENERATION');

        $api = $this->pokeClient->getPokemonGeneration($generation);
        
        $data = [];
        $pokemon_list = $api['pokemon_species'];

        for ($i=0; $i<count($pokemon_list); $i++) {
            $pokemon_details = $this->pokeClient->getPokemonCsv($pokemon_list[$i]['name']);
            if ($pokemon_details != [] )$data[]=$pokemon_details;
        }

        $handle = fopen($file, 'w');

        if (!$handle) {
            $output->writeln(sprintf('Unable to create file %s', $file));
            return 1;
        }

        foreach ($data as $fields) {
            fputcsv($handle, $fields);
        }

        fclose($handle);

        $output->writeln(sprintf('Data exported to %s', $file));

        return 0;

    }
}
