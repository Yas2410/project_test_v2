<?php

namespace App\Controller\Admin;

use App\Entity\Family;
use App\Form\FamilyType;
use App\Repository\FamilyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class FamilyController extends AbstractController
{
    /**
     * //* @Route("/admin/families", name="admin_families_list")
     * @param FamilyRepository $familyRepository
     * @return Response
     */

    public function families(FamilyRepository $familyRepository)
    {
        $families = $familyRepository->findAll();
        return $this->render('admin/families/families.html.twig', [
            'families' => $families
        ]);
    }

    /**
     * @route("admin/family/show/{id}", name="admin_family_show")
     * @param FamilyRepository $familyRepository
     * @param $id
     * @return Response
     */
    public function Family(FamilyRepository $familyRepository, $id)
    {
        $family = $familyRepository->find($id);

        return $this->render('admin/families/family.html.twig', [
            'families' => $family
        ]);
    }

    /**
     * @route("admin/Family/search", name="admin_search_family")
     * @param FamilyRepository $familyRepository
     * @param Request $request
     * @return Response
     */
    public function searchByFamily(FamilyRepository $familyRepository, Request $request)
    {
        $search = $request->query->get('search');
        $families = $familyRepository->getByWordInFamily($search);

        return $this->render('admin/families/search_family.html.twig', [
            'search' => $search, 'families' => $families
        ]);
    }

    /**
     * @route("admin/family/update/{id}", name="admin_update_family")
     * @param Request $request
     * @param FamilyRepository $familyRepository
     * @param EntityManagerInterface $entityManager
     * @param $id
     * @return Response
     */
    public function updateFamily(
        Request $request,
        FamilyRepository $familyRepository,
        EntityManagerInterface $entityManager,
        $id
    )
    {
        $family = $familyRepository->find($id);
        $formFamily = $this->createForm(FamilyType::class, $family);
        $formFamily->handleRequest($request);
        if ($formFamily->isSubmitted() && $formFamily->isValid()) {
            $entityManager->persist($family);
            $entityManager->flush();

            $this->addFlash('sucess', "La fiche famille a bien été modifiée !");
        }

        return $this->render('admin/families/update_family.html.twig', [
            'formFamily'=>$formFamily->createView()
        ]);
    }

    /**
     * @route("admin/family/delete/{id}", name="admin_delete_family")
     * @param FamilyRepository $familyRepository
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function deleteFamily(
        Request $request,
        FamilyRepository $familyRepository,
        EntityManagerInterface $entityManager,
        $id
    )
    {
        $family = $familyRepository->find($id);
        $entityManager->remove($family);
        $entityManager->flush();

        $this->addFlash('sucess', "La fiche famille a bien été supprimée !");

        return $this->redirectToRoute('admin_families_list');
    }

}