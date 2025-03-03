<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/category')]
final class CategoryController extends AbstractController
{
    #[Route('/get', name: 'app_category_index', methods: ['GET'])]
    public function index(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findAll();

        return $this->json($categories, Response::HTTP_OK, [], ['groups' => ['category:read']]);
    }


    #[Route('/new', name: 'app_category_new', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN', message: 'vous ne pouvez pas ajouter dn\'élément')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {

        $data = json_decode($request->getContent(), true);

        $category = new Category();
        $category->setName($data['name']);

        $entityManager->persist($category);
        $entityManager->flush();


        return $this->json($category, Response::HTTP_CREATED, [], ['groups' => ['category:read']]);
    }


    #[Route('/{id}', name: 'app_category_show', methods: ['GET'])]
    public function show(Category $category): Response
    {
        return $this->json($category, Response::HTTP_OK, [], ['groups' => ['category:read']]);
    }


    #[Route('/{id}', name: 'app_category_edit', methods: ['PUT'])]
    #[IsGranted('ROLE_ADMIN', message: 'vous ne pouvez pas modifier dn\'élément')]
    public function edit(Request $request, Category $category, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);

        if (isset($data['name'])) {
            $category->setName($data['name']);
        }

        $entityManager->flush();

        return $this->json($category, Response::HTTP_OK, [], ['groups' => ['category:read']]);
    }


    #[Route('/{id}', name: 'app_category_delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN', message: 'vous ne pouvez pas supprimer cet élément')]
    public function delete(Category $category, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($category);
        $entityManager->flush();
    
        return $this->json(['message' => 'Category deleted successfully'], Response::HTTP_OK);
    }
    
}
