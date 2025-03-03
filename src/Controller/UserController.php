<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api')]
class UserController extends AbstractController
{
    #[Route('/register', name: 'api_register', methods: ['POST'])]
    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer
    ): JsonResponse {
        $user = $serializer->deserialize($request->getContent(), User::class, 'json');

        $hashedPassword = $passwordHasher->hashPassword($user, $user->getPassword());
        $user->setPassword($hashedPassword);

        $user->setRoles(['ROLE_USER']);

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json(['message' => 'User registered successfully'], JsonResponse::HTTP_CREATED);
    }

    #[Route('/user/{id}', name: 'api_user_update', methods: ['PATCH'])]
    public function updateUser(
        int $id,
        Request $request,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer
    ): JsonResponse {
        $user = $entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            return new JsonResponse(['message' => 'Utilisateur non trouvé'], 404);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['email'])) {
            $user->setEmail($data['email']);
        }

        if (isset($data['roles'])) {
            $user->setRoles($data['roles']);
        }

        if (isset($data['subscriptionToNewsletter'])) {
            $user->setSubscriptionToNewsletter($data['subscriptionToNewsletter']);
        }

        if (isset($data['password'])) {
            return new JsonResponse(['message' => 'La modification du mot de passe n\'est pas autorisée via cette route.'], 400);
        }
        
        $entityManager->persist($user);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Utilisateur mis à jour avec succès'], 200);
    }
}
