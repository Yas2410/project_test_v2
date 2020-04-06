<?php

namespace App\Controller\Front;

use App\Entity\Gallery;
use App\Repository\GalleryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GalleryController extends AbstractController
{
    /**
     * @Route("front/pictures", name="front_pictures")
     * @param GalleryRepository $galleryRepository
     * @return Response
     */
    public function pictures(GalleryRepository $galleryRepository)
    {
        $pictures = $galleryRepository->findAll();
        return $this->render('front/gallery/pictures.html.twig', [
            'pictures' => $pictures
        ]);
    }

    /**
     * @route("front/gallery/show/{id}", name="front_gallery")
     * @param GalleryRepository $galleryRepository
     * @param $id
     * @return Response
     */
    public function event(GalleryRepository $galleryRepository, $id)
    {
        $gallery = $galleryRepository->find($id);

        return $this->render('front/gallery/gallery.html.twig', [
            'gallery' => $gallery
        ]);
    }

    /**
     * @route("front/gallery/search", name="front_gallery_search")
     * @param GalleryRepository $galleryRepository
     * @param Request $request
     * @return Response
     */
    public function searchByGallery(GalleryRepository $galleryRepository, Request $request)
    {
        $search = $request->query->get('search');
        $pictures = $galleryRepository->getByWordInGallery($search);

        return $this->render('front/gallery/search.html.twig', [
            'search' => $search, 'events' => $pictures
        ]);
    }

}