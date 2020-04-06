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
     * //* @Route("admin/articles", name="admin_article_list")
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
        return $this->render('admin/article/articles.html.twig', [
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

        return $this->render('admin/article/article.html.twig', [
            'article' => $article
        ]);
    }

// Création d'une nouvelle route pour INSERER des articles.

    /**
     * @route("admin/article/insert", name="admin_article_insert")
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
// Création d'un nouvel article afin de le lier au formulaire.
        $article = new Article();
// Création du formulaire que je lie au nouvel article.
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
// Enfin, on persiste l'article
            $entityManager->persist($article);
            $entityManager->flush();

// Création d'un message Flash indiquant que l'article a bien été créé :
            $this->addFlash('success', 'Votre article a bien été créé !');

        }
        return $this->render('admin/article/insert.html.twig', [
            'formArticle' => $formArticle->createView()
        ]);

    }

// Création d'une nouvelle route pour SUPPRIMER des articles.

    /**
     * @route("admin/article/delete", name="admin_article_delete")
     * @param ArticleRepository $articleRepository
     * @param EntityManagerInterface $entityManager
     * @param $id
     * @return Response
     */
    public function deleteArticle(
        ArticleRepository $articleRepository,
        EntityManagerInterface $entityManager,
        $id
    )
    {
        $article = $articleRepository->find($id);
        $entityManager->remove($article);
        $entityManager->flush();

        return new Response("L'article a bien été supprimé !");
    }

// Création d'une nouvelle route pour SUPPRIMER des articles via l'URL.
    /**
     * @route("admin/article/delete/{id}", name="admin_article_delete")
     */
/*  public function deleteEventUrl(ArticleRepository $articleRepository, EntityManagerInterface $entityManager, $id)
    {
        $article = $articleRepository->find($id);
        $entityManager->remove($article);
        $entityManager->flush();

        return new Response("L'article a bien été supprimé !");
    }
*/

// Création d'une nouvelle route pour METTRE A JOUR des articles.

    /**
     * @route("admin/article/update/{id}", name="admin_article_update")
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

        }

        return $this->render('admin/article/insert.html.twig', [
            'formArticle' => $formArticle->createView()
        ]);
    }


// Création d'une nouvelle route pour RECHERCHER des articles.

    /**
     * @route("admin/article/search", name="admin_article_search")
     * @param ArticleRepository $articleRepository
     * @param Request $request
     * @return Response
     */
    public function searchByArticle(ArticleRepository $articleRepository, Request $request)
    {
        $search = $request->query->get('search');
        $articles = $articleRepository->getByWordInArticle($search);

        return $this->render('admin/article/search.html.twig', [
            'search' => $search, 'articles' => $articles
        ]);
    }
}

