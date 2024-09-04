<?php
// src/DataFixtures/SchoolFixtures.php

namespace App\DataFixtures;

use App\Entity\School;
use App\Entity\Training;
use App\Entity\Module;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as FakerFactory;

class SchoolFixtures extends Fixture
{
    private array $schools = [];
    private array $trainings = [];
    private array $modules = [];
    private array $entities = [];

    public function load(ObjectManager $manager)
    {
        $faker = FakerFactory::create('fr_FR');

        // Création des écoles
        for ($i = 0; $i < 3; $i++) {
            $school = new School();
            $school->setName($faker->company());
            $school->setDescription($faker->sentence());
            $manager->persist($school);
            $this->schools[] = $school;
        }

        // Création des modules
        for ($i = 0; $i < 6; $i++) {
            $module = new Module();
            $module->setName($faker->sentence());
            $module->setDescription($faker->sentence());
            $manager->persist($module);
            $this->modules[] = $module;
        }

        // Création des trainings et association aléatoire aux écoles et modules
        for ($i = 0; $i < 6; $i++) {
            $training = new Training();
            $training->setName($faker->sentence());
            $training->setDescription($faker->sentence());

            // Associer aléatoirement une école au training
            $randomSchool = $this->schools[array_rand($this->schools)];
            $training->setSchool($randomSchool);

            // Associer aléatoirement 3 modules distincts au training
            $randomModules = array_rand($this->modules, 3);
            foreach ($randomModules as $moduleIndex) {
                $training->addModule($this->modules[$moduleIndex]);
            }

            $manager->persist($training);
            $this->trainings[] = $training;
        }

        $manager->flush();
    }

    public function getSchools(): array
    {
        return $this->schools;
    }

    public function getTrainings(): array
    {
        return $this->trainings;
    }

    public function getModules(): array
    {
        return $this->modules;
    }

    public function getEntities()
    {
        array_merge($this->trainings,  $this->modules, $this->schools);
        return $this->entities;
    }
}
