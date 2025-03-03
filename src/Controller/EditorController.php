<?php

namespace App\Controller;

use App\Entity\Editor;
use App\Form\EditorType;
use App\Repository\EditorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/editor')]
final class EditorController extends AbstractController
{
    #[Route('/get', name: 'app_editor_index', methods: ['GET'])]
    public function index(EditorRepository $editorRepository): Response
    {
        $editors = $editorRepository->findAll(); 
        return $this->json($editors, Response::HTTP_OK, [], ['groups' => ['editor:read']]);
    }

    #[Route('/new', name: 'app_editor_new', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN', message: 'vous ne pouvez pas ajouter dn\'élément')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);

        $editor = new Editor();
        $editor->setName($data['name']);
        $editor->setCountry($data['country']);

        $entityManager->persist($editor);
        $entityManager->flush();

        return $this->json($editor, Response::HTTP_CREATED, [], ['groups' => ['editor:read']]);
    }


    #[Route('/{id}', name: 'app_editor_show', methods: ['GET'])]
    public function show(Editor $editor): Response
    {
        return $this->render('editor/show.html.twig', [
            'editor' => $editor,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_editor_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN', message: 'vous ne pouvez pas ajouter dn\'élément')]
    public function edit(Request $request, Editor $editor, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);
    
        if (isset($data['name'])) {
            $editor->setName($data['name']);
        }
        if (isset($data['country'])) {
            $editor->setCountry($data['country']);
        }
   
        $entityManager->flush();
    
        return $this->json($editor, Response::HTTP_OK, [], ['groups' => ['editor:read']]);
    }
    


    #[Route('/{id}', name: 'app_editor_delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN', message: 'vous ne pouvez pas supprimer cet élément')]
    public function delete(Editor $editor, EntityManagerInterface $entityManager): Response
    {
        if (!$editor) {
            return $this->json(['message' => 'Editor not found'], Response::HTTP_NOT_FOUND);
        }

        $entityManager->remove($editor);
        $entityManager->flush();

        return $this->json(['message' => 'Editor deleted successfully'], Response::HTTP_OK);
    }

}
