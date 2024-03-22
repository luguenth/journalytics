<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

use App\Entity\Journalist;
use App\Entity\Newspaper;
use Symfony\Component\HttpFoundation\Request;

class JournalistController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/journalist', name: 'app_journalist')]
    public function index(): Response
    {
        // Get all the journalists from the database
        $journalists = $this->entityManager->getRepository(Journalist::class)->findBy([], ['name' => 'ASC']);

        // Render the journalist page
        return $this->render('journalist/index.html.twig', [
            'journalists' => $journalists,
        ]);
    }

    #[Route('/journalist/{id}', name: 'app_journalist_show')]
    public function show(int $id): Response
    {
        // Get the journalist from the database
        $journalist = $this->entityManager->getRepository(Journalist::class)->find($id);

        // get all newspapers from the journalist
        $newspapers = $this->entityManager->getRepository(Newspaper::class)->getAllNewspapersFromJournalist($id);

        // Render the journalist page
        return $this->render('journalist/show.html.twig', [
            'journalist' => $journalist,
            'newspapers' => $newspapers,
        ]);
    }

    #[Route('/journalist/{id}/mark', name: 'app_journalist_mark_as_generic')]
    public function markAsGeneric(int $id, Request $request): Response
    {
        // Get the journalist from the database
        $journalist = $this->entityManager->getRepository(Journalist::class)->find($id);

        // Mark the journalist as generic
        $journalist->setIsGeneric(true);

        // Save the changes to the database
        $this->entityManager->flush();

        // Get the URL of the referring page
        $referer = $request->headers->get('referer');

        // Redirect back to the referring page
        return $this->redirect($referer);
    }

    #[Route('/journalist/{id}/unmark', name: 'app_journalist_mark_as_not_generic')]
    public function markAsNotGeneric(int $id, Request $request): Response
    {
        // Get the journalist from the database
        $journalist = $this->entityManager->getRepository(Journalist::class)->find($id);

        // Mark the journalist as not generic
        $journalist->setIsGeneric(false);

        // Save the changes to the database
        $this->entityManager->flush();

        // Get the URL of the referring page
        $referer = $request->headers->get('referer');

        // Redirect back to the referring page
        return $this->redirect($referer);
    }
}
