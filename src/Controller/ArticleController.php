<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;


use App\Entity\Article;
use App\Entity\Journalist;
use App\Entity\Newspaper;

class ArticleController extends AbstractController
{

    private $entityManager;
    private $logger;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    #[Route('/article', name: 'app_article')]
    public function index(): Response
    {
        // Get all the articles from the database who have an author
        $articles = $this->entityManager->getRepository(Article::class)->findAllArticlesWithAuthors();

        // Render the article page
        return $this->render('article/index.html.twig', [
            'articles' => $articles,
        ]);
    }

    #[Route('/article/{id}', name: 'app_article_show')]
    public function show(int $id): Response
    {
        // Get the article from the database
        $article = $this->entityManager->getRepository(Article::class)->find($id);

        // Render the article page
        return $this->render('article/show.html.twig', [
            'article' => $article,
        ]);
    }

    #[Route('/api/v1/articles', name: 'app_post_articles', methods: ['POST'])]
    public function postArticles(Request $request): JsonResponse
    {
        $this->logger->info('Received articles request:', ['request' => $request->getContent()]);
        $requestData = json_decode($request->getContent(), true);

        //Validate request data

        if (!isset($requestData['articles']) || !is_array($requestData['articles'])) {
            return new JsonResponse(['error' => 'Invalid request data'], JsonResponse::HTTP_BAD_REQUEST);
        }

        foreach ($requestData['articles'] as $articleData) {
            $newspaperName = $articleData['newspaper_name'];
            $newspaperLink = $articleData['newspaper_link'];
            $articleTitle = $articleData['article_title'];
            $articleLink = $articleData['article_link'];
            $publishingDate = new \DateTime($articleData['article_publishing_date']);
            $articleDescription = $articleData['article_description'];
            $authorNames = $articleData['author_names'];

            // Create or retrieve the Newspaper entity
            $newspaper = $this->entityManager->getRepository(Newspaper::class)->findOneBy([
                'name' => $newspaperName,
            ]);
            if (!$newspaper) {
                $newspaper = new Newspaper();
                $newspaper->setName($newspaperName);
                $this->entityManager->persist($newspaper);
                $this->entityManager->flush();
            }
            $article = $this->entityManager->getRepository(Article::class)->findOneBy([
                'title' => $articleTitle,
            ]);
            // Create the Article entity
            if (!$article) {
                $article = new Article();
                $article->setTitle($articleTitle);
                $article->setLink($articleLink);
                $article->setDate($publishingDate);
                $article->setDescription($articleDescription);
                $article->addNewspaper($newspaper);
                $this->entityManager->persist($article);
                $this->entityManager->flush();

                if (isset($authorNames[0])) {
                    foreach ($authorNames[0] as $authorName) {
                        if ($authorName === "unknown") {
                            continue;
                        }
                        $journalist = $this->entityManager->getRepository(Journalist::class)->findOneBy([
                            'name' => $authorName,
                        ]);
                        if (!$journalist) {
                            $journalist = new Journalist();
                            $journalist->setName($authorName);
                            $this->entityManager->persist($journalist);
                            $this->entityManager->flush();
                        }
                        $article->addJournalist($journalist);
                    }
                }
            }
        }

        // Save the entities to the database
        $this->entityManager->flush();

        return new JsonResponse(['success' => 'Articles saved successfully'], JsonResponse::HTTP_CREATED);
    }
}
