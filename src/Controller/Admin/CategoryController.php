<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route(path: "/admin/category", name: "admin.category.")]
class CategoryController extends AbstractController
{
    #[Route(name: "index")]
    public function index(CategoryRepository $repository)
    {
        return $this->render('admin/category/index.html.twig', [
            'categories' => $repository->findAll(),
        ]);
    }

    #[Route(path: '/create', name: "create")]
    public function create(Request $request, EntityManagerInterface $em)
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($category);
            $em->flush();
            $this->addFlash("success", "La catégorie à bien été ajoutée");
            return $this->redirectToRoute('admin.category.index');
        }
        return $this->render("admin/category/create.html.twig", [
            'form' => $form,
        ]);
    }

    #[Route(path: '/{id}', name: "edit", requirements: ['id' => Requirement::DIGITS], methods: ['GET', 'POST'])]
    public function edit(Category $category, Request $request, EntityManagerInterface $em)
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash("success", "La catégorie à bien été modifiée.");
            return $this->redirectToRoute("admin.category.index");
        }
        return $this->render('admin/category/create.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);

    }

    #[Route(path: '/{id}', name: "delete", requirements: ['id' => Requirement::DIGITS], methods: ['DELETE'])]
    public function delete(Category $category, EntityManagerInterface $em)
    {
        $em->remove($category);
        $em->flush();
        $this->addFlash('success', 'La catégorie a bien été supprimée');
        return $this->redirectToRoute('admin.category.index');
    }
}