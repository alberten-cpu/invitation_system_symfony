<?php

namespace App\Controller\Api;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface; 
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;




class UserController extends AbstractController
{

    private function logInUser(User $user)
    {
        $token = new UsernamePasswordToken($user, 'name', $user->getRoles());
        $this->container->get('security.token_storage')->setToken($token);

        // // Fire the login event
        // $event = new InteractiveLoginEvent($user, $token);
        // $this->container->get('event_dispatcher')->dispatch($event);

        // Save the user's ID to the session
        $this->container->get('session')->set('_security_main', serialize($token));
    }



    public function index(): Response
    {
        return $this->render('api/user/index.html.twig');
    }

    /**
     * @Route("/register", name="user_register", methods={"POST"})
     */
    public function register(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $postData = $request->request->all();
        $user = new User();
        $user->setEmail($postData['email']);
        $user->setUserName($postData['userName']);
        $user->setRoles(['ROLE_USER']);

        // $encodedPassword = $passwordHasher->hashPassword($user,$postData['password']);
        $encodedPassword = $passwordHasher->hashPassword($user, $postData['password']);
        $user->setPassword($encodedPassword);

        // Validate the user entity
        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            // There are validation errors, return an error response
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }

            return new JsonResponse(['errors' => $errorMessages], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Proceed with registration if validation passes

        // Persist the entity
        $entityManager->persist($user);

        // Flush changes to the database
        $entityManager->flush();

        return new JsonResponse(['message' => 'User registered successfully'], JsonResponse::HTTP_CREATED);
    }

    // #[Route('/api/login', name: 'api_user_login', methods: ['POST'])]
    public function login(Request $request, EntityManagerInterface $entityManager,UserPasswordHasherInterface $passwordHasher, JWTTokenManagerInterface $jwtManager): JsonResponse
    {
        $content = $request->getContent();
        $postData = json_decode($content, true);

        if ($postData === null) {

            $postData = $request->request->all();
        }

        if (!isset($postData['email']) || !isset($postData['password'])) {
            return $this->json(['message' => 'Invalid request'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $postData['email']]);

        if (!$user || !$passwordHasher->isPasswordValid($user, $postData['password'])) {
            return $this->json(['message' => 'Invalid credentials'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $token = $jwtManager->create($user);
        $this->logInUser($user);


        return $this->getUser();
    }
}
