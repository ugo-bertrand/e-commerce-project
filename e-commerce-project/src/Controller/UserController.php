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
use App\Exception\ForbiddenRequestException;
use App\Exception\NotFoundException;
use App\Exception\UnauthorizedException;
use DateTimeImmutable;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class UserController extends AbstractController
{

    #[Route('/api/register', name: 'api_register')]
    public function createUser(LoggerInterface $logger, UserRepository $userRepository, Request $request ): JsonResponse
    {
        try{
            $logger->info("Début de la création d'un utilisateur.");
            $bodyRequest = $request->toArray();
            if(count($bodyRequest) != 5){
                throw new NotFoundException("Le contenu ne doit pas être vide, veuillez mettre les valeurs correspondantes");
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
                'message' => "L'utilisateur a été créé"
            ],201);
        }
        catch(Exception $e){
            $logger->error("Une erreur est survenue");
            $error = new JsonErrorController();
            if($e->getCode() == 404){
                return $error->showError($e->getCode());
            }
            else{
                return $error->showDefaultError();
            }
        }
        
    }

    #[Route('/api/login', name: 'api_login')]
    public function loginUser(UserRepository $userRepository, LoggerInterface $logger, Request $request): JsonResponse
    {
        try{
            $body = $request->toArray();
            if($body['login'] == "" || $body['password'] == ""){
                throw new NotFoundException("Le contenu ne doit pas être vide, veuillez mettre les valeurs correspondantes");
            }
            $loginUser = $body['login'];
            $userInfo = $userRepository->getUserByLogin($loginUser);
            $passwordIsOk = password_verify($body['password'], $userInfo->getPassword());
            if($passwordIsOk){
                $logger->info("La connexion de l'utilisateur est OK génération du token en cours");
            }
            else{
                throw new UnauthorizedException("Le mot de passe ne correspond pas");
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

            $request->getSession()->set('token',$jwt);
            
            return new JsonResponse([
                'token' => $jwt
            ],200);
        }
        catch(Exception $e){
            $logger->error("Une erreur est survenue lors de l'authentification de l'utilisateur");
            $error = new JsonErrorController();
            if($e->getCode() == 404){
                return $error->showError($e);
            }
            else if($e->getCode() == 401){
                return $error->showError($e);
            }
            else{
                return $error->showDefaultError();
            }
        }
    }

    #[Route('/api/users', name: 'api_user_update')]
    public function updateUser(LoggerInterface $logger, Request $request, AuthorizationController $authorization, UserRepository $userRepository): JsonResponse
    {
        try{
            $token = $authorization->extractTokenFromAuthorizationHeader($request,$logger);

            //todo return the error
            $logger->info("Décodage du token en cours");
            $decoded = JWT::decode($token, new Key($_ENV['SECRET_KEY'], 'HS256'));
            $userId = $decoded->userId;

            $logger->info("Récupération des données à modifier pour l'utilisateur");
            $body = $request->toArray();

            $logger->info("Récupération des données via la base de données de l'utilisateur");
            $newUser = new User();
            $newUser->setLogin($body['login']);
            $hashPassword = password_hash($body['password'], PASSWORD_DEFAULT);
            $newUser->setPassword($hashPassword);
            $newUser->setEmail($body['email']);
            $newUser->setFirstname($body['firstname']);
            $newUser->setLastname($body['lastname']);

            $logger->info("Enregistrement des nouvelles données dans la base de données");

            $userRepository->updateUserById($userId,$newUser);

            return new JsonResponse([
                "statut" => "Votre utilisateur a bien été modifié"
            ],204);

        }
        catch(Exception $e){
            $logger->error("Une erreur est survenue lors de la modification de l'utilisateur.");
            $error = new JsonErrorController();
            if($e->getCode() == 403){
                return $error->showError($e);
            }
            else if($e->getCode() == 401){
                return $error->showError($e);
            }
            else if($e->getCode() == 404){
                return $error->showError($e);
            }
            else{
                return $error->showDefaultError();
            }
        }
        
    }

    #[Route('/api/users', name: 'api_user_display')]
    public function displayUser(LoggerInterface $logger, Request $request, AuthorizationController $authorization, UserRepository $userRepository): JsonResponse
    {
        try{
            $token = $authorization->extractTokenFromAuthorizationHeader($request,$logger);
            $logger->info("Décodage du token en cours");
            $decoded = JWT::decode($token, new Key($_ENV['SECRET_KEY'],'HS256'));
            $logger->info("decodage ok");
            $userId = $decoded->userId;
            $user = $userRepository->getUserById($userId);

            return new JsonResponse([
                'login' => $user->getLogin(),
                'email' => $user->getEmail(),
                'firstname' => $user->getFirstname(),
                'lastname' => $user->getLastname()
            ],200);

        }
        catch(Exception $e){
            $logger->error("Une erreur est survenue lors de la récupération des données de l'utilisateur");
            $error = new JsonErrorController();
            if($e->getCode() == 401){
                return $error->showError($e);
            }
            else if($e->getCode() == 404){
                return $error->showError($e);
            }
            else{
                return $error->showDefaultError();
            }
        }
    }
}
