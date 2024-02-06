<?php 

namespace App\Command;

use App\Entity\Movie;
use App\Entity\Character;
use App\Repository\CharacterRepository;
use App\Repository\MovieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(
    name: 'starwars:import',
    description: 'Import all the movies and 30 characters from Star Wars API'
)]
class StarWarsImport extends Command
{
    public const API_URL = 'https://swapi.dev/api/';
    public const MAX_CHARS = 30; 

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private HttpClientInterface $client,
        private MovieRepository $movieRepository,
        private CharacterRepository $characterRepository,
    ) {
        parent::__construct();  
    }

    protected function configure(): void
    {
        $this->setHelp('This commands allows to import data from the Star Wars movies and characters')
            ->addOption('model', 'm', InputOption::VALUE_OPTIONAL, 'If set, the user is created as an administrator');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $model = $input->getOption('model');

        if (!$model || $model == 'movies') {
            $this->getMovies($output);
        }

        if (!$model || $model == 'characters') {
            $this->getCharacters($output);
        }

        return Command::SUCCESS;
    }

    private function getMovies(OutputInterface $output)
    {
        $output->writeln("Importing Movies");

        $response = $this->client->request(
            'GET',
            self::API_URL.'films'
        );

        $films = $response->toArray()['results'] ?? [];

        foreach($films as $film) {
            $title = $film['title'];

            $existingMovie = $this->movieRepository->findOneBy(['name' => $title]);

            if (!$existingMovie) {
                $movie = new Movie();
                $movie->setName($title);

                $this->entityManager->persist($movie);
                $this->entityManager->flush();
            }
        }

        $output->writeln("Finished import of Movies");
    }

    private function getCharacters(OutputInterface $output)
    {
        $output->writeln("Importing Characters");

        $response = $this->client->request(
            'GET',
            self::API_URL.'people'
        );

        $people = array_slice($response->toArray()['results'] ?? [], 0, self::MAX_CHARS);

        foreach($people as $person) {
            $existingCharacter = $this->characterRepository->findOneBy(['name' => $person['name']]);

            if (!$existingCharacter) {
                $character = new Character();
                $character->setName($person['name']);
                $character->setMass($person['mass']);
                $character->setHeight($person['height']);
                $character->setGender($person['gender']);

                $this->getCharacterMovies($character, $person['films']);

                $this->entityManager->persist($character);
                $this->entityManager->flush();
            }
        }

        $output->writeln("Finished import of Characters");
    }

    private function getCharacterMovies(Character $character, array $films): void
    {
        $movies = [];

        foreach ($films as $film) {
            $response = $this->client->request(
                'GET',
                $film
            );

            $title = $response->toArray()['title'] ?? '';

            if ($title) {
                $existingMovie = $this->movieRepository->findOneBy(['name' => $title]);
                
                if ($existingMovie) {
                    $character->addMovie($existingMovie);
                }
            }
        }


    }

}