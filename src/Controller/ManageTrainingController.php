<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\TrainingRepository;
use App\Entity\School;
use App\Entity\Training;
use App\Entity\Module;
use App\Repository\ModuleRepository;

class ManageTrainingController extends AbstractController
{

	#[Route('/manage_training/{id}', name: 'manage_training', methods: ['GET'])]
	public function manageTraining(int $id, EntityManagerInterface $entityManager, Request $request): Response
	{
		// Je récupère la formation demandée
		$training = $entityManager->getRepository(Training::class)->find($id);

		// Si la formation n'existe pas, je renvoie une 404
		if (!$training) {
			throw $this->createNotFoundException('La formation demandée n\'existe pas.');
		}

		// Je récupère le module de la requête, si un module est passé
		$moduleId = $request->query->get('module');
		if ($moduleId) {
			$module = $entityManager->getRepository(Module::class)->find($moduleId);

			// module bien lié à la formation, sinon 404
			if ($module === null || !$training->getModules()->contains($module)) {
				throw $this->createNotFoundException('Le module demandé n\'est pas lié à la formation.');
			} else {
				// Suppression du module
				$training->removeModule($module);
				$entityManager->remove($module);
				$entityManager->flush();

				// Je redirige vers la route de la formation après suppression
				return $this->redirectToRoute('manage_training', ['id' => $training->getId()]);
			}
		}

		// Je rends la vue pour gérer la formation
		return $this->render('training/manage.html.twig', [
			'training' => $training,
		]);
	}
}
