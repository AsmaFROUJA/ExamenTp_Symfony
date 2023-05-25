<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/article', name: 'app_article')]
    public function index(): Response
    {
        return $this->render('article/index.html.twig', [
            'controller_name' => 'ArticleController',
        ]);
    }

    // ...

    public function list(): Response
    {
        $articleRepository = $this->entityManager->getRepository(Article::class);
        $articles = $articleRepository->findBy([], ['createdAt' => 'DESC']);

        return $this->render('article/list.html.twig', [
            'articles' => $articles,
        ]);
    }

    public function create(Request $request): Response
    {
        $article = new Article();

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($article);
            $this->entityManager->flush();

            return $this->redirectToRoute('article_list');
        }

        return $this->render('article/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function show($id): Response
    {
        $articleRepository = $this->entityManager->getRepository(Article::class);
        $article = $articleRepository->find($id);

        if (!$article) {
            throw $this->createNotFoundException('L\'article n\'existe pas.');
        }

        return $this->render('article/show.html.twig', [
            'article' => $article,
        ]);
    }

    public function update(Request $request, $id): Response
    {
        $articleRepository = $this->entityManager->getRepository(Article::class);
        $article = $articleRepository->find($id);

        if (!$article) {
            throw $this->createNotFoundException('L\'article n\'existe pas.');
        }

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            return $this->redirectToRoute('article_show', ['id' => $article->getId()]);
        }

        return $this->render('article/update.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function delete($id): Response
    {
        $articleRepository = $this->entityManager->getRepository(Article::class);
        $article = $articleRepository->find($id);

        if (!$article) {
            throw $this->createNotFoundException('Article existe pas');
        }

        $this->entityManager->remove($article);
        $this->entityManager->flush();

        return $this->redirectToRoute('article_list');
    }
}
