<?php

namespace App\Controller\Front;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    /**
     * @Route("front/articles", name="front_articles")
     * @param ArticleRepository $articleRepository
     * @return Response
     */
    public function articles(ArticleRepository $articleRepository)
    {
        $articles = $articleRepository->findAll();
        return $this->render('front/article/articles.html.twig', [
            'articles' => $articles
        ]);
    }

    /**
     * @route("front/article/show/{id}", name="front_article")
     * @param ArticleRepository $articleRepository
     * @param $id
     * @return Response
     */
    public function article(ArticleRepository $articleRepository, $id)
    {
        $article = $articleRepository->find($id);

        return $this->render('front/article/article.html.twig', [
            'article' => $article
        ]);
    }

    /**
     * @route("front/article/search", name="front_article_search")
     * @param ArticleRepository $articleRepository
     * @param Request $request
     * @return Response
     */
    public function searchByArticle(ArticleRepository $articleRepository, Request $request)
    {
        $search = $request->query->get('search');
        $articles = $articleRepository->getByWordInArticle($search);

        return $this->render('front/article/search.html.twig', [
            'search' => $search, 'articles' => $articles
        ]);
    }

}