<?php

namespace App\Controller\Front;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use App\Repository\EventRepository;
use App\Repository\PictureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeParentsController extends AbstractController
{
    /**
     * @Route("/home", name="home_parents")
     * @param ArticleRepository $articleRepository
     * @param EventRepository $eventRepository
     * @param PictureRepository $galleryRepository
     * @return Response
     */
    public function homeParents(
        ArticleRepository $articleRepository,
        EventRepository $eventRepository,
        PictureRepository $galleryRepository
    )
    {
        $lastArticles = $articleRepository->findBy([], ['id' => 'DESC'], 3, 0);
        $lastEvents = $eventRepository->findBy([], ['id' => 'DESC'], 3, 0);
        return $this->render('front/homeParents.html.twig', [
            'articles' => $lastArticles,
            'events' => $lastEvents
        ]);


    }
}
