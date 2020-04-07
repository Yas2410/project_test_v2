<?php

namespace App\Controller\Front;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

//Ici, je créé ma classe "ArticleController" qui sera nommée à l'identique que mon fichier.

class ArticleController extends AbstractController
{

/* La synthaxe d'une @route : annotation et non commentaire car "/**".
 Chaque route fait la correspondance entre l'url à afficher et le controller à exécuter pour permettre
cet affichage. */

    /**
     * //* @Route("/parents/articles", name="articles")
     * @param ArticleRepository $articleRepository
     * @return Response
     */

/* J'instancie ici la classe ArticleRepository dans la variable $articleRepository.
Pour cela, on utilise l'"autowire" de Symfony : Automatise la configuration des services.
Valable pour toutes les classes, à l'exception des entités. */

    public function articles(ArticleRepository $articleRepository)
    {

/* Récupérer le repository des Articles, car c'est la classe Repository
 qui me permet de sélectionner les évènements en BDD. */

        $articles = $articleRepository->findAll();
        return $this->render('front/articles/articles.html.twig', [
            'articles' => $articles
        ]);
    }

// Création d'une nouvelle route avec une WildCArd (= une variable).
// Ici, "$id".

    /**
     * @route("parents/article/show/{id}", name="article")
     * @param ArticleRepository $articleRepository
     * @param $id
     * @return Response
     */
    public function article(ArticleRepository $articleRepository, $id)
    {
        $article = $articleRepository->find($id);

        return $this->render('front/articles/article.html.twig', [
            'article' => $article
        ]);
    }


// Création d'une nouvelle route pour RECHERCHER des articles.

    /**
     * @route("parents/article/search", name="search_article")
     * @param ArticleRepository $articleRepository
     * @param Request $request
     * @return Response
     */
    public function searchByArticle(ArticleRepository $articleRepository, Request $request)
    {
        $search = $request->query->get('search');
        $articles = $articleRepository->getByWordInArticle($search);

        return $this->render('front/articles/search_article.html.twig', [
            'search' => $search, 'articles' => $articles
        ]);
    }
}

