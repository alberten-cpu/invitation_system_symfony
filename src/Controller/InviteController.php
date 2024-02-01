<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Invitation;
use App\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class InviteController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function sendInvitation(Request $request): JsonResponse
    {
        $content = $request->getContent();
        $postData = json_decode($content, true);

        if ($postData === null) {

            $postData = $request->request->all();
        }


        // Validate request data
        if (!isset($postData['sender_email']) || !isset($postData['receiver_email'])) {
            return $this->json(['message' => 'Invalid request'], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Find sender and receiver users
        $sender = $this->getUserByEmail($postData['sender_email']);
        $receiver = $this->getUserByEmail($postData['receiver_email']);

        // Check if both users exist
        if (!$sender || !$receiver) {
            return $this->json(['message' => 'Sender or receiver not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        // Check if the invitation already exists
        if ($this->invitationExists($sender, $receiver)) {
            return $this->json(['message' => 'Invitation already sent'], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Create and persist the invitation
        $invitation = new Invitation();
        $invitation->setSender($sender);
        $invitation->setReceiver($receiver);
        $invitation->setAction(Invitation::ACTION_SEND); 
        $invitation->setIsRead(false);
        $invitation->setCreatedAt(new \DateTime());

        $this->entityManager->persist($invitation);
        $this->entityManager->flush();

        return $this->json(['message' => 'Invitation sent successfully'], JsonResponse::HTTP_CREATED);
    }

    private function getUserByEmail(string $email): ?User
    {
        return $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
    }

    private function invitationExists(User $sender, User $receiver): bool
    {
        return $this->entityManager->getRepository(Invitation::class)
            ->findOneBy(['sender' => $sender, 'receiver' => $receiver]) !== null;
    }
}

