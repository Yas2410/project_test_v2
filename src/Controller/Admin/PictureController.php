<?php

namespace App\Controller\Admin;

use App\Entity\Picture;
use App\Form\PictureType;
use App\Repository\PictureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class PictureController extends AbstractController
{
    /**
     * //* @Route("/admin/pictures", name="admin_pictures_list")
     * @param PictureRepository $pictureRepository
     * @return Response
     */

    public function pictures(PictureRepository $pictureRepository)
    {
        $pictures = $pictureRepository->findAll();
        return $this->render('admin/pictures/pictures.html.twig', [
            'pictures' => $pictures
        ]);
    }

    /**
     * @route("admin/picture/show/{id}", name="admin_picture_show")
     * @param PictureRepository $pictureRepository
     * @param $id
     * @return Response
     */
    public function picture(PictureRepository $pictureRepository, $id)
    {
        $picture = $pictureRepository->find($id);

        return $this->render('admin/pictures/picture.html.twig', [
            'pictures' => $picture
        ]);
    }

    /**
     * @route("admin/picture/insert", name="admin_picture_event")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param $slugger
     * @return Response
     */

    public function insertPicture(Request $request,
                                  EntityManagerInterface $entityManager,
                                  SluggerInterface $slugger
    )
    {
        $picture = new Picture();
        $formPicture = $this->createForm(PictureType::class, $picture);
        $formPicture->handleRequest($request);

        if ($formPicture->isSubmitted() && $formPicture->isValid()) {

            $pictureFile = $formPicture->get('picturefile')->getData();

            if ($pictureFile) {

                $originalFilename = pathinfo($pictureFile->getClientOriginalName(), PATHINFO_FILENAME);

                $safeFilename = $slugger->slug($originalFilename);

                $newFilename = $safeFilename . '-' . uniqid() . '.' . $pictureFile->guessExtension();

                $pictureFile->move(
                    $this->getParameter('pictureFile_directory'),
                    $newFilename);

                $picture->setPictureFile($newFilename);
            }

            $entityManager->persist($picture);
            $entityManager->flush();

            $this->addFlash('success', "Le fichier a bien été inséré !");

        }
        return $this->render('admin/pictures/insert_picture.html.twig', [
            'formPicture' => $formPicture->createView()
        ]);

    }

    /**
     * @route("admin/picture/search", name="admin_search_picture")
     * @param PictureRepository $pictureRepository
     * @param Request $request
     * @return Response
     */
    public function searchByPicture(PictureRepository $pictureRepository, Request $request)
    {
        $search = $request->query->get('search');
        $pictures = $pictureRepository->getByWordInPicture($search);

        return $this->render('admin/pictures/search_picture.html.twig', [
            'search' => $search, 'pictures' => $pictures
        ]);
    }

    /**
     * @route("admin/picture/update/{id}", name="admin_update_picture")
     * @param Request $request
     * @param PictureRepository $pictureRepository
     * @param EntityManagerInterface $entityManager
     * @param $id
     * @return Response
     */
    public function updatePicture(
        Request $request,
        PictureRepository $pictureRepository,
        EntityManagerInterface $entityManager,
        $id
    )
    {
        $picture = $pictureRepository->find($id);
        $formPicture = $this->createForm(PictureType::class, $picture);
        $formPicture->handleRequest($request);
        if ($formPicture->isSubmitted() && $formPicture->isValid()) {
            $entityManager->persist($picture);
            $entityManager->flush();

            $this->addFlash('sucess', "La photo a bien été modifiée !");
        }

        return $this->render('admin/pictures/update_picture.html.twig', [
            'formPicture'=>$formPicture->createView()
        ]);
    }

    /**
     * @route("admin/picture/delete/{id}", name="admin_delete_picture")
     * @param PictureRepository $pictureRepository
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function deletePicture(
        Request $request,
        PictureRepository $pictureRepository,
        EntityManagerInterface $entityManager,
        $id
    )
    {
        $picture = $pictureRepository->find($id);
        $entityManager->remove($picture);
        $entityManager->flush();

        $this->addFlash('sucess', "La photo a bien été supprimée !");

        return $this->redirectToRoute('admin_pictures_list');
    }

}