<?php

namespace App\Controller\Front;

use App\Entity\Picture;
use App\Repository\PictureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GalleryController extends AbstractController
{
    /**
     * @Route("front/pictures", name="front_pictures")
     * @param PictureRepository $galleryRepository
     * @return Response
     */
    public function pictures(PictureRepository $galleryRepository)
    {
        $pictures = $galleryRepository->findAll();
        return $this->render('front/gallery/pictures.html.twig', [
            'pictures' => $pictures
        ]);
    }

    /**
     * @route("front/gallery/show/{id}", name="front_gallery")
     * @param PictureRepository $galleryRepository
     * @param $id
     * @return Response
     */
    public function event(PictureRepository $galleryRepository, $id)
    {
        $gallery = $galleryRepository->find($id);

        return $this->render('front/gallery/gallery.html.twig', [
            'gallery' => $gallery
        ]);
    }

    /**
     * @route("front/gallery/search", name="front_gallery_search")
     * @param PictureRepository $galleryRepository
     * @param Request $request
     * @return Response
     */
    public function searchByGallery(PictureRepository $galleryRepository, Request $request)
    {
        $search = $request->query->get('search');
        $pictures = $galleryRepository->getByWordInGallery($search);

        return $this->render('front/gallery/search_article.html.twig', [
            'search' => $search, 'events' => $pictures
        ]);
    }

}