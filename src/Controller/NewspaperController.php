<?php

namespace App\Controller;

use App\Entity\Newspaper;
use App\Entity\Journalist;

use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NewspaperController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/newspaper', name: 'app_newspaper')]
    public function index(): Response
    {
        // Get all the newspapers from the database
        $newspapers = $this->entityManager->getRepository(Newspaper::class)->findAll();

        // Render the newspaper page
        return $this->render('newspaper/index.html.twig', [
            'newspapers' => $newspapers,
        ]);
    }

    #[Route('/newspaper/{id}', name: 'app_newspaper_show')]
    public function show(int $id): Response
    {
        // Get the newspaper from the database
        $newspaper = $this->entityManager->getRepository(Newspaper::class)->find($id);

        // get all journalists from the newspaper
        $journalists = $this->entityManager->getRepository(Journalist::class)->getAllJournalistsFromNewspaper($id);

        // get all articles from the newspaper
        $articles = $this->entityManager->getRepository(Article::class)->getAllArticlesFromNewspaper($id);

        // Render the newspaper page
        return $this->render('newspaper/show.html.twig', [
            'newspaper' => $newspaper,
            'journalists' => $journalists,
            'articles' => $articles,
        ]);
    }
}
