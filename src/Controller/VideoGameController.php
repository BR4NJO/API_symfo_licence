<?php

namespace App\Controller;

use App\Entity\VideoGame;
use App\Entity\Editor;
use App\Entity\Category;
use App\Form\VideoGameType;
use App\Repository\VideoGameRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[Route('/video_game')]
final class VideoGameController extends AbstractController
{
    #[Route('/get', name: 'app_video_game_index', methods: ['GET'])]
    public function index(VideoGameRepository $videoGameRepository, SerializerInterface $serializer): Response
    {
        $videoGames = $videoGameRepository->findAll();

        $jsonContent = $serializer->serialize($videoGames, 'json', ['groups' => 'video_game:read']);
        
        return new JsonResponse($jsonContent, Response::HTTP_OK, [], true);
    }


    #[Route('/new', name: 'app_video_game_new', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN', message: 'vous ne pouvez pas supprimer cet élément')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);
    
        $videoGame = (new VideoGame())
            ->setTitle($data['title'])
            ->setReleaseDate(new \DateTime($data['releaseDate']))
            ->setDescription($data['description']);

    
        if ($editor = $data['editor'] ?? null) {
            $editorEntity = $entityManager->getRepository(Editor::class)->findOneBy(['name' => $editor['name']]);
            if (!$editorEntity) return $this->json(['error' => 'Editor not found'], Response::HTTP_BAD_REQUEST);
            $videoGame->setEditor($editorEntity);
        }
    
        foreach ($data['categories'] ?? [] as $categoryData) {
            if ($category = $entityManager->getRepository(Category::class)->findOneBy(['name' => $categoryData['name']])) {
                $videoGame->addCategory($category);
            }
        }
    
        $entityManager->persist($videoGame);
        $entityManager->flush();
    
        return $this->json($videoGame, Response::HTTP_CREATED, [], ['groups' => ['video_game:read']]);
    }
    


    #[Route('/{id}', name: 'app_video_game_show', methods: ['GET'])]
    public function show(VideoGame $videoGame): Response
    {
        return $this->json($videoGame); 
    }

    #[Route('/{id}/edit', name: 'app_video_game_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN', message: 'vous ne pouvez pas supprimer cet élément')]
    public function edit(Request $request, VideoGame $videoGame, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);

        if (isset($data['title'])) $videoGame->setTitle($data['title']);
        if (isset($data['releaseDate'])) $videoGame->setReleaseDate(new \DateTime($data['releaseDate']));
        if (isset($data['description'])) $videoGame->setDescription($data['description']);

        if ($editor = $data['editor'] ?? null) {
            $editorEntity = $entityManager->getRepository(Editor::class)->findOneBy(['name' => $editor['name']]);
            if ($editorEntity) $videoGame->setEditor($editorEntity);
        }

        if (isset($data['categories'])) {
            $videoGame->getCategories()->clear(); 
            foreach ($data['categories'] as $categoryData) {
                if ($category = $entityManager->getRepository(Category::class)->findOneBy(['name' => $categoryData['name']])) {
                    $videoGame->addCategory($category);
                }
            }
        }

        $entityManager->flush();

        return $this->json($videoGame, Response::HTTP_OK, [], ['groups' => ['video_game:read']]);
    }

    #[Route('/{id}', name: 'app_video_game_delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN', message: 'vous ne pouvez pas supprimer cet élément')]
    public function delete(VideoGame $videoGame, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($videoGame);
        $entityManager->flush();

        return $this->json(['message' => 'Jeu Supprimé'], Response::HTTP_OK);
    }

}
