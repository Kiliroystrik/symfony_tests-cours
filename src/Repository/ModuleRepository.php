<?php

namespace App\Repository;

use App\Entity\Module;
use App\Entity\Training;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Module>
 */
class ModuleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Module::class);
    }

    public function findModuleNotLinkedToTraining(Training $training): ?Module
    {
        // Requête DQL pour récupérer un module qui n'est pas lié à ce training
        $qb = $this->createQueryBuilder('m')
            ->leftJoin('m.trainings', 't')
            ->where('t.id IS NULL OR t.id != :trainingId')
            ->setParameter('trainingId', $training->getId())
            ->setMaxResults(1); // Limite à un module

        return $qb->getQuery()->getOneOrNullResult();
    }
}
