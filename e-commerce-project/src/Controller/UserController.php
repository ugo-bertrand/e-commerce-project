<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Log\LoggerInterface;
use App\Entity\User;
use App\Repository\UserRepository;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use App\Controller\JsonErrorController;
use DateTimeImmutable;
use Firebase\JWT\JWT;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserController extends AbstractController
{

    #[Route('/api/register', name: 'api_register')]
    public function createUser(LoggerInterface $logger, UserRepository $userRepository, Request $request ): JsonResponse
    {
        try{
            $logger->info("Début de la création d'un utilisateur.");
            $bodyRequest = $request->toArray();
            if(count($bodyRequest) != 5){
                throw new NotFoundHttpException("Le contenu ne correspond pas pour créer correctement l'utilisateur",null, 404);
            }
            $logger->info("Récupération des données pour créer l'utilisateur");

            //cryptage du mot de passe
            $hashPassword = password_hash($bodyRequest['password'], PASSWORD_DEFAULT);
            $newUser = new User();
            $newUser->setLogin($bodyRequest['login']);
            $newUser->setPassword($hashPassword);
            $newUser->setEmail($bodyRequest['email']);
            $newUser->setFirstname($bodyRequest['firstname']);
            $newUser->setLastname($bodyRequest['lastname']);
            $userRepository->save($newUser,true);

            return new JsonResponse([
                'message' => "L'utilisateur a été créer"
            ],201);
        }
        catch(Exception $e){
            $logger->error("Une erreur est survenue lors de la création de l'utilisateur {message}", ['message' => $e->getMessage()]);
            $error = new JsonErrorController();
            if($e->getCode() == 404){
                return $error->showError($e);
            }
            else{
                $error->showDefaultError();
            }
        }
        
    }

    #[Route('/api/login', name: 'api_login')]
    public function loginUser(UserRepository $userRepository, LoggerInterface $logger, Request $request): JsonResponse
    {
        try{
            $body = $request->toArray();
            if($body['login'] == "" || $body['password'] == ""){
                throw new NotFoundHttpException("Le contenu ne doit pas être vide", null, 404);
            }
            $loginUser = $body['login'];
            $userInfo = $userRepository->getUserByLogin($loginUser);
            $logger->debug("Les informations de l'utilisateur : {login}", ['login' => $userInfo->getLogin()]);
            $logger->debug("Les informations de l'utilisateur : {password}", ['password' => $userInfo->getPassword()]);
            $passwordIsOk = password_verify($body['password'], $userInfo->getPassword());
            if($passwordIsOk){
                $logger->info("La connexion de l'utilisateur est OK génération du token en cours");
            }
            else{
                throw new NotFoundHttpException("Mot de passe incorrect", null, 401);
            }

            $issuer = "e-commerce.com";
            $expiration_time = time() +  3600 ;
            $date = new DateTimeImmutable();

            $logger->debug("Génération du payload en cours");
            $payload = array(
                "iat" => $date->getTimestamp(),
                "iss" => $issuer,
                "nbf" => $date->getTimestamp(),
                "exp" => $expiration_time,
                "userId" => $userInfo->getId(),
                "login" => $userInfo->getLogin(),
                "email" => $userInfo->getEmail()
            );

            $logger->debug("Encodage en cours");
            $jwt = JWT::encode($payload, $_ENV['SECRET_KEY'],'HS256');

            $_SESSION['token'] = $jwt;
            
            return new JsonResponse([
                'token' => $jwt
            ]);
        }
        catch(Exception $e){
            $error = new JsonErrorController();
            if($e->getCode() == 404){
                return $error->showError($e);
            }
            else{
                return $error->showDefaultError();
            }
        }
    }

    #[Route('/api/users', name: 'api_user_update')]
    public function updateUser(): JsonResponse
    {
        return new JsonResponse([
            'code' => 200
        ]);
    }

    #[Route('/api/users', name: 'api_user_display')]
    public function displayUser(): JsonResponse
    {
        return new JsonResponse([
            'code' => 200
        ]);
    }
}
