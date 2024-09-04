<?php

namespace Functional\Controller;

use App\DataFixtures\SchoolFixtures;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\Training;
use App\Repository\TrainingRepository;
use phpDocumentor\Reflection\Types\Boolean;

class TrainingControllerTest extends WebTestCase
{
    private $entityManager;
    private $client;
    private $fixture;

    protected function setUp(): void
    {
        // Initialiser le client et l'EntityManager
        $this->client = static::createClient();
        $this->entityManager = $this->client->getContainer()->get('doctrine')->getManager();

        // Charger les fixtures
        $this->loadFixtures();
    }

    private function loadFixtures(): void
    {
        $this->fixture = new SchoolFixtures();
        $this->fixture->load($this->entityManager);
    }

    /**
     * Test the route '/search_training' without any arguments.
     *
     * This function tests if the route '/search_training' returns a status code of 200.
     * It also verifies that the twig template contains a list of all trainings from the database.
     *
     * @return void
     */
    public function testWhithoutArguments(): void //******* Fonctionne pour le moment
    {
        // test si la route fonctionne
        $crawler = $this->client->request('GET', '/search_training');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        // Vérification des données du twig qui doit contenir la liste de tout les trainings de la BDD
        $trainings = $this->entityManager->getRepository(Training::class)->findAll();
        $this->assertCount(count($trainings), $crawler->filter('tbody tr'));
    }

    /**
     * Test the route '/search_training' with selected modules.
     *
     * This function tests if the route '/search_training' returns a status code of 200
     * when called with a list of selected modules. It also verifies that the twig template
     * contains the list of trainings that have all the selected modules.
     *
     * @return void
     */
    public function testSelectedModulesIdFromRequest(): void //******* Fonctionne pour le moment
    {
        // Je récupère des modules
        $modules = $this->getThreeRandomModules();

        // J'appel la méthode makeRequest
        $this->makeRequest($modules);

        // A partir de là, normalement la route renvoie un 200.
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode()); //***** Ok pour le 200

        // Je récupère la liste des trainings qui on obligatoirement l'intégralité des modules
        $trainings = $this->entityManager->getRepository(Training::class)->findByModules($modules);

        // Vérification des données du twig
        foreach ($trainings as $training) {
            $this->assertSelectorTextContains('td', $training->getName());
        }
    }

    /**
     * Test the route '/search_training' with selected modules that match any module.
     *
     * This function tests if the route '/search_training' returns a status code of 200
     * when called with a list of selected modules. It also verifies that the twig template
     * contains the list of trainings that have at least one of the selected modules.
     *
     * @return void
     */
    public function testSelectedModulesIdFromRequestWhithMatchAnyModule(): void
    {
        // Je récupère des modules
        $modules = $this->getThreeRandomModules();

        // J'appel la méthode makeRequest
        $this->makeRequest($modules, true);

        // A partir de là, normalement la route renvoie un 200.
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode()); //***** Ok pour le 200

        // Vérification des données du twig

        // Je récupère la liste des trainings qui on au moins un des modules
        $trainings = $this->entityManager->getRepository(Training::class)->findByAnyModule($modules);

        // Vérification des données du twig
        foreach ($trainings as $training) {
            $this->assertSelectorTextContains('td', $training->getName());
        }
    }


    protected function tearDown(): void
    {
        $entities = $this->fixture->getEntities();

        foreach ($entities as $entity) {
            $this->entityManager->remove($entity);
        }

        $this->entityManager->flush();
        parent::tearDown();
        $this->entityManager->close();
        $this->entityManager = null; // éviter les fuites de mémoire
    }


    /**
     * ToolBox
     */
    private function getThreeRandomModules(): array
    {
        $modules = $this->fixture->getModules();
        $randomModules = array_rand($modules, 3);
        return $randomModules;
    }

    private function makeRequest(array $selectedModules = [], Bool $matchAnyModule = false): void
    {
        $this->client->request('GET', '/search_training', [
            'modules' => $selectedModules,
            'match_any_module' => $matchAnyModule,
        ]);
    }
}
