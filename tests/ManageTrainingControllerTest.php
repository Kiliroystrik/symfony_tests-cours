<?php

namespace Functional\Controller;

use App\DataFixtures\SchoolFixtures;
use App\Entity\Module;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\Training;
use phpDocumentor\Reflection\Types\Boolean;

class ManageTrainingControllerTest extends WebTestCase
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
     * Test the manage training functionality.
     *
     * This function randomly selects a training from the fixture and tests the manage training page.
     * It asserts that the page returns a 200 status code and displays the training name in the H1 tag.
     * If the training has modules, it verifies the module list and the redirection link.
     * If the training has no modules, it verifies that the "No modules" message is displayed and there are no module list items.
     *
     * @return void
     */
    public function testManageTraining(): void // ? ************* Fonctionne pour le moment
    {
        // Je récupère un training de ma fixture aléatoirement
        $trainings = $this->fixture->getTrainings();

        $randTraining = $trainings[array_rand($trainings)];

        // test si la route fonctionne
        $this->getPage($randTraining->getId());

        // On test la page avec un training de ma fixture aléatoirement si c'est bon retour 200
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        // Lors de l'implémentation j'aurai en H1 le nom du training
        $this->assertSelectorTextContains('h1', $randTraining->getName());

        // J'aurai aussi graçe à mon jeu de données des modules si training en possède, dans une liste
        if (count($randTraining->getModules()) > 0) {
            foreach ($randTraining->getModules() as $module) {
                // je vérifie l'attribut href de redirection de mon <a href="{{ path('manage_training', {id: training.id, module: module.id}) }}">Supprimer</a>
                $this->assertSelectorExists('ul.list-modules li a[href="/manage_training/' . $randTraining->getId() . '?module=' . $module->getId() . '"]');

                // optionnel mais je peux aussi vérifier le bouton
                $this->assertSelectorTextContains('ul.list-modules li a', 'Supprimer');
            }
        } else {
            // Ici j'écrirais un no modules
            $this->assertSelectorTextContains('ul.list-modules', 'No modules');

            // Je vérifie qu'il n'y a pas de li sur ma page
            $this->assertSelectorNotExists('ul.list-modules li');
        }
    }

    /**
     * Tests the manage training functionality when no training is provided.
     *
     * This function tests two scenarios:
     * 1. When no training ID is passed in the URL, it asserts a 404 status code.
     * 2. When a non-existent training ID is passed, it asserts a 404 status code.
     *
     * @return void
     */
    public function testManageTrainingNoTraining(): void // ? ************* Fonctionne pour le moment
    {
        // ici training n'est pas passé dans l'url
        $this->client->request('GET', '/manage_training');
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());

        // ici training n'existe pas
        $this->getPage(999999);
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
    }

    /**
     * Tests the deletion of a training module.
     *
     * This function tests the deletion of a training module by iterating over the modules of a training,
     * making a deletion request for each module, and verifying the redirection and the absence of the module
     * on the page after deletion.
     *
     * @return void
     */
    public function testTrainingModuleDeletion(): void // ? ************* Fonctionne pour le moment
    {
        // Ici je fais le choix de rester sur mes fixtures en m'assurant que le training possède au moins un module
        $training = $this->getTrainings();

        // Maintenant je peux appeler la route pour supprimer mes modules un par un
        foreach ($training->getModules() as $module) {
            $this->makeDeletionRequest($training->getId(), $module);

            // Vérification de la redirection après suppression
            $this->assertResponseRedirects("/manage_training/{$training->getId()}");

            // Je suis la redirection
            $this->client->followRedirect();

            // Je vérifie que le module n'est plus sur la page
            $this->assertSelectorNotExists('ul.list-modules li:contains("' . $module->getName() . '")');
        }
    }


    /**
     * Tests the non deletion of a training module when the module ID is not assigned to the training.
     *
     * This function tests the non deletion of a training module by selecting a training and a module that do not belong together,
     * making a deletion request, and verifying that a 404 status code is returned.
     *
     * @return void
     */
    public function testTrainingModuleDeletionIdIsNotAssignedToTraining(): void
    {
        // Je récupère un training qui a au moins un module
        $training = $this->getTrainings();

        // Je récupère un module qui n'est pas lié à ce training
        // ? J'ai un peu triché pour pas m'embeter avec la création de tableau a cause des collections. Du coup j'ai créé une méthode dans le repository pour ce faire.
        $unlinkedModule = $this->entityManager->getRepository(Module::class)->findModuleNotLinkedToTraining($training);

        // j'appelle la route de suppression qui doit me renvoyer juste après une erreur 404 car le module n'est pas lié à ce training
        $this->makeDeletionRequest($training->getId(), $unlinkedModule);

        // Je vérifie que la page renvoie une erreur 404
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
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

    private function getPage(int $id): void
    {
        $this->client->request('GET', "/manage_training/{$id}");
    }

    private function makeDeletionRequest(int $id, Module $module = null): void
    {
        $this->client->request('GET', "/manage_training/{$id}", [
            'module' => $module ? $module->getId() : null,
        ]);
    }

    private function getTrainings(): ?Training
    {
        $trainings = $this->fixture->getTrainings();

        foreach ($trainings as $randTraining) {
            // Si le training a des modules et n'est pas celui à éviter
            if (count($randTraining->getModules()) > 0) {
                return $randTraining;
            }
        }

        // Au cas ou aucun training n'a de module
        return null;
    }
}
