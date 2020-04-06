<?php

namespace App\Controller\Admin;

use App\Entity\Gallery;
use App\Form\GalleryType;
use App\Repository\GalleryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;


class GalleryController extends AbstractController
{
    /**
     * //* @Route("admin/galleries", name="admin_gallery_list")
     * @param GalleryRepository $galleryRepository
     * @return Response
     */

    public function galleries(GalleryRepository $galleryRepository)
    {

        $galleries = $galleryRepository->findAll();
        return $this->render('admin/gallery/galleries.html.twig', [
            'galleries' => $galleries
        ]);
    }

    /**
     * @route("admin/gallery/show/{id}", name="admin_gallery_show")
     * @param GalleryRepository $galleryRepository
     * @param $id
     * @return Response
     */
    public function gallery(GalleryRepository $galleryRepository, $id)
    {
        $gallery = $galleryRepository->find($id);

        return $this->render('admin/gallery/gallery.html.twig', [
            'gallery' => $gallery
        ]);
    }

    /**
     * @route("admin/gallery/insert", name="admin_gallery_insert")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param $slugger
     * @return Response
     */

    public function insertEvent(Request $request,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger
    )
    {
        $gallery = new Gallery ();
        $formGallery = $this->createForm(GalleryType::class, $gallery);
        $formGallery->handleRequest($request);

        if ($formGallery->isSubmitted() && $formGallery->isValid()) {

            $galleryFile = $formGallery->get('galleryfile')->getData();

            if ($galleryFile) {

                $originalFilename = pathinfo($galleryFile->getClientOriginalName(), PATHINFO_FILENAME);

                $safeFilename = $slugger->slug($originalFilename);

                $newFilename = $safeFilename . '-' . uniqid() . '.' . $galleryFile->guessExtension();

                $galleryFile->move(
                    $this->getParameter('galleryFile_directory'),
                    $newFilename);

                $gallery->setGalleryFile($newFilename);
            }
            $entityManager->persist($gallery);
            $entityManager->flush();

            $this->addFlash('success', 'Votre upload a bien été crée !');

        }
        return $this->render('admin/gallery/insert.html.twig', [
            'formGallery' => $formGallery->createView()
        ]);

    }

    /**
     * @route("admin/gallery/delete", name="admin_gallery_delete")
     * @param GalleryRepository $galleryRepository
     * @param EntityManagerInterface $entityManager
     * @param $id
     * @return Response
     */
    public function deleteGallery(
        GalleryRepository $galleryRepository,
        EntityManagerInterface $entityManager,
        $id
    )
    {
        $gallery = $galleryRepository->find($id);
        $entityManager->remove($gallery);
        $entityManager->flush();

        return new Response("Votre photo a bien été supprimée !");
    }

    /**
     * @route("admin/gallery/update/{id}", name="admin_gallery_update")
     * @param Request $request
     * @param GalleryRepository $galleryRepository
     * @param EntityManagerInterface $entityManager
     * @param $id
     * @return Response
     */
    public function updateGallery(
        Request $request,
        GalleryRepository $galleryRepository,
        EntityManagerInterface $entityManager,
        $id
    )
    {
        $gallery = $galleryRepository->find($id);
        $formGallery = $this->createForm(GalleryType::class, $gallery);
        $formGallery->handleRequest($request);
        if ($formGallery->isSubmitted() && $formGallery->isValid()) {
            $entityManager->persist($gallery);
            $entityManager->flush();

        }

        return $this->render('admin/gallery/insert.html.twig', [
            'formGallery' => $formGallery->createView()
        ]);
    }


    /**
     * @route("admin/gallery/search", name="admin_gallery_search")
     * @param GalleryRepository $galleryRepository
     * @param Request $request
     * @return Response
     */
    public function searchByGallery(GalleryRepository $galleryRepository, Request $request)
    {
        $search = $request->query->get('search');
        $galleries = $galleryRepository->getByWordInGallery($search);

        return $this->render('admin/gallery/search.html.twig', [
            'search' => $search, 'galleries' => $galleries
        ]);
    }
}

