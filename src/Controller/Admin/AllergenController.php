<?php

namespace App\Controller\Admin;

use App\Entity\Allergen;
use App\Form\AllergenType;
use App\Repository\AllergenRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class AllergenController extends AbstractController
{
    /**
     * //* @Route("/admin/allergens", name="admin_allergens_list")
     * @param AllergenRepository $allergenRepository
     * @return Response
     */

    public function allergens(AllergenRepository $allergenRepository)
    {
        $allergens = $allergenRepository->findAll();
        return $this->render('admin/allergens/families.html.twig', [
            'allergens' => $allergens
        ]);
    }

    /**
     * @route("admin/allergen/show/{id}", name="admin_allergen_show")
     * @param AllergenRepository $allergenRepository
     * @param $id
     * @return Response
     */
    public function allergen(AllergenRepository $allergenRepository, $id)
    {
        $allergen = $allergenRepository->find($id);

        return $this->render('admin/allergens/family.html.twig', [
            'allergens' => $allergen
        ]);
    }

    /**
     * @route("admin/allergen/insert", name="admin_insert_allergen")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param $slugger
     * @return Response
     */

    public function insertAllergen(Request $request,
                                  EntityManagerInterface $entityManager,
                                  SluggerInterface $slugger
    )
    {
        $allergen = new Allergen();
        $formAllergen = $this->createForm(AllergenType::class, $allergen);
        $formAllergen->handleRequest($request);

        if ($formAllergen->isSubmitted() && $formAllergen->isValid()) {

            $allergenFile = $formAllergen->get('allergenfile')->getData();

            if ($allergenFile) {

                $originalFilename = pathinfo($allergenFile->getClientOriginalName(), PATHINFO_FILENAME);

                $safeFilename = $slugger->slug($originalFilename);

                $newFilename = $safeFilename . '-' . uniqid() . '.' . $allergenFile->guessExtension();

                $allergenFile->move(
                    $this->getParameter('allergenFile_directory'),
                    $newFilename);

                $allergen->setAllergenFile($newFilename);
            }

            $entityManager->persist($allergen);
            $entityManager->flush();

            $this->addFlash('success', "Le nouveau type d'allergie a bien été créé !");

        }
        return $this->render('admin/allergens/insert_allergen.html.twig', [
            'formAllergen' => $formAllergen->createView()
        ]);

    }

    /**
     * @route("admin/allergen/search", name="admin_search_allergen")
     * @param AllergenRepository $allergenRepository
     * @param Request $request
     * @return Response
     */
    public function searchByAllergen(AllergenRepository $allergenRepository, Request $request)
    {
        $search = $request->query->get('search');
        $allergens = $allergenRepository->getByWordInAllergen($search);

        return $this->render('admin/allergens/search_family.html.twig', [
            'search' => $search, 'allergens' => $allergens
        ]);
    }

    /**
     * @route("admin/allergen/update/{id}", name="admin_update_allergen")
     * @param Request $request
     * @param AllergenRepository $allergenRepository
     * @param EntityManagerInterface $entityManager
     * @param $id
     * @return Response
     */
    public function updateAllergen(
        Request $request,
        AllergenRepository $allergenRepository,
        EntityManagerInterface $entityManager,
        $id
    )
    {
        $allergen = $allergenRepository->find($id);
        $formAllergen = $this->createForm(AllergenType::class, $allergen);
        $formAllergen->handleRequest($request);
        if ($formAllergen->isSubmitted() && $formAllergen->isValid()) {
            $entityManager->persist($allergen);
            $entityManager->flush();

            $this->addFlash('sucess', "L'allergie a bien été modifiée !");
        }

        return $this->render('admin/allergens/update_family.html.twig', [
            'formAllergen'=>$formAllergen->createView()
        ]);
    }

    /**
     * @route("admin/allergen/delete/{id}", name="admin_delete_allergen")
     * @param AllergenRepository $allergenRepository
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function deleteAllergen(
        Request $request,
        AllergenRepository $allergenRepository,
        EntityManagerInterface $entityManager,
        $id
    )
    {
        $allergen = $allergenRepository->find($id);
        $entityManager->remove($allergen);
        $entityManager->flush();

        $this->addFlash('sucess', "L'allergie a bien été supprimée !");

        return $this->redirectToRoute('admin_allergens_list');
    }

}