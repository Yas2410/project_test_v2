<?php

namespace App\Controller\Admin;

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
     * //* @Route("/admin/articles", name="admin_articles_list")
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
        return $this->render('admin/articles/articles.html.twig', [
            'articles' => $articles
        ]);
    }

// Création d'une nouvelle route avec une WildCArd (= une variable).
// Ici, "$id".

    /**
     * @route("admin/article/show/{id}", name="admin_article_show")
     * @param ArticleRepository $articleRepository
     * @param $id
     * @return Response
     */
    public function article(ArticleRepository $articleRepository, $id)
    {
        $article = $articleRepository->find($id);

        return $this->render('admin/articles/article.html.twig', [
            'article' => $article
        ]);
    }

// Création d'une nouvelle route pour INSERER des articles.

    /**
     * @route("admin/article/insert", name="admin_insert_article")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param $slugger
     * @return Response
     */

    public function insertArticle(Request $request,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger
    )
    {
// Création d'un nouvel articles afin de le lier au formulaire.
        $article = new Article();
// Création du formulaire que je lie au nouvel articles.
        $formArticle = $this->createForm(ArticleType::class, $article);
// Je demande à mon formulaire (ici $formArticle) de gérer les données POST.
        $formArticle->handleRequest($request);
// Si les données envoyées depuis le formulaire sont valides :
        if ($formArticle->isSubmitted() && $formArticle->isValid()) {

// Ici, je récupère la valeur du fichier uploadé.
            $articleFile = $formArticle->get('articlefile')->getData();

// On vérifie ici si un élément a bien été envoyé :
            if ($articleFile) {

// On vérifie le nom du fichier uploadé :
                $originalFilename = pathinfo($articleFile->getClientOriginalName(), PATHINFO_FILENAME);

// On sort les caractères spéciaux du nom de ce dernier.
                $safeFilename = $slugger->slug($originalFilename);

// On ajoute au nom du fichier un identifiant unique.
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $articleFile->guessExtension();

// On déplace le fichier uploadé dans le dossier précisé au préalable en paramètre du fichier services.yaml.
                $articleFile->move(
                    $this->getParameter('articleFile_directory'),
                    $newFilename);

// On enregistre le nom du fichier uploadé en BDD.
                $article->setArticleFile($newFilename);
            }
// Enfin, on persiste l'articles
            $entityManager->persist($article);
            $entityManager->flush();

// Création d'un message Flash indiquant que l'articles a bien été créé :
            $this->addFlash('success', "L'article a bien été créé !");

        }
        return $this->render('admin/articles/insert_article.html.twig', [
            'formArticle' => $formArticle->createView()
        ]);

    }

// Création d'une nouvelle route pour RECHERCHER des articles.

    /**
     * @route("admin/article/search", name="admin_search_article")
     * @param ArticleRepository $articleRepository
     * @param Request $request
     * @return Response
     */
    public function searchByArticle(ArticleRepository $articleRepository, Request $request)
    {
        $search = $request->query->get('search');
        $articles = $articleRepository->getByWordInArticle($search);

        return $this->render('admin/articles/search_article.html.twig', [
            'search' => $search, 'articles' => $articles
        ]);
    }

// Création d'une nouvelle route pour METTRE A JOUR des articles.

    /**
     * @route("admin/article/update/{id}", name="admin_update_article")
     * @param Request $request
     * @param ArticleRepository $articleRepository
     * @param EntityManagerInterface $entityManager
     * @param $id
     * @return Response
     */
    public function updateArticle(
        Request $request,
        ArticleRepository $articleRepository,
        EntityManagerInterface $entityManager,
        $id
    )
    {
        $article = $articleRepository->find($id);
        $formArticle = $this->createForm(ArticleType::class, $article);
        $formArticle->handleRequest($request);
        if ($formArticle->isSubmitted() && $formArticle->isValid()) {
            $entityManager->persist($article);
            $entityManager->flush();

            $this->addFlash('sucess', "L' article a bien été modifié !");
        }

        return $this->render('admin/articles/update_article.html.twig', [
            'formArticle'=>$formArticle->createView()
        ]);
    }

// Création d'une nouvelle route pour SUPPRIMER des articles.

    /**
     * @route("admin/article/delete/{id}", name="admin_delete_article")
     * @param ArticleRepository $articleRepository
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function deleteArticle(
        Request $request,
        ArticleRepository $articleRepository,
        EntityManagerInterface $entityManager,
        $id
    )
    {
        $article = $articleRepository->find($id);
        $entityManager->remove($article);
        $entityManager->flush();

        $this->addFlash('sucess', "L'article a bien été supprimé !");

        return $this->redirectToRoute('admin_articles_list');
    }

}

