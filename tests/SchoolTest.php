<?php

namespace Functional\Controller;

use App\DataFixtures\SchoolFixtures;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\School;
use App\Entity\Training;
use App\Entity\Module;

class SchoolTest extends WebTestCase
{
    private $entityManager;
    private $client;
    private $fixture;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = $this->client->getContainer()->get('doctrine')->getManager();

        // Load fixtures
        $this->loadFixtures();
    }

    private function loadFixtures(): void
    {
        $this->fixture = new SchoolFixtures();
        $this->fixture->load($this->entityManager);
    }

    public function testTrainingWithDatabase()
    {
        $repository = $this->entityManager->getRepository(Training::class);

        $training = $repository->findOneBy([]);

        $crawler = $this->client->request('GET', '/training/' . $training->getId());

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertCount(3, $crawler->filter('body > ul li'));
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
}
