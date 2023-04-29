<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Log\LoggerInterface;
use App\Entity\User;
use App\Entity\Product;
use App\Entity\Cart;
use App\Entity\Order;
use App\Repository\UserRepository;
use App\Repository\ProductRepository;
use App\Repository\CartRepository;
use App\Repository\OrderRepository;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use App\Controller\JsonErrorController;
use DateTime;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class CartController extends AbstractController
{
    #[Route('/api/carts/{productId}', name: 'api_add_product_cart')]
    public function addCartProduct(LoggerInterface $logger, Request $request, AuthorizationController $authorization, CartRepository $cartRepository, UserRepository $userRepository, ProductRepository $productRepository, int $productId): JsonResponse
    {
        try{
            $token = $authorization->extractTokenFromAuthorizationHeader($request,$logger);

            $logger->info("Décodage du token en cours");
            $decoded = JWT::decode($token, new Key($_ENV['SECRET_KEY'], 'HS256'));
            $userId = $decoded->userId;

            $user = $userRepository->getUserById($userId);

            $product = $productRepository->getProductById($productId);
            $cartRepository->addProduct($user,$product);

            return new JsonResponse(["statut" => "Le produit a bien été ajouté dans le panier"],200);

        }
        catch(Exception $e){
            $logger->error("Une erreur est survenue lors de l'ajout du produit dans le panier.");
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

    #[Route('/api/carts/{productId}', name: 'api_remove_product_cart')]
    public function removeCartProduct(LoggerInterface $logger, Request $request, AuthorizationController $authorization, CartRepository $cartRepository, UserRepository $userRepository, ProductRepository $productRepository, int $productId): JsonResponse
    {
        try{
            $token = $authorization->extractTokenFromAuthorizationHeader($request,$logger);

            $logger->info("Décodage du token en cours");
            $decoded = JWT::decode($token, new Key($_ENV['SECRET_KEY'], 'HS256'));
            $userId = $decoded->userId;
            $user = $userRepository->getUserById($userId);

            $product = $productRepository->getProductById($productId);
            $cartRepository->removeProduct($user,$product);

            return new JsonResponse(["statut" => "Le produit a bien été supprimé du panier"],200);

        }
        catch(Exception $e){
            $logger->error("Une erreur est survenue lors de la suppresion du produit du panier.");
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

    #[Route('/api/carts', name: 'api_cart')]
    public function displayCart(LoggerInterface $logger, Request $request, AuthorizationController $authorization, CartRepository $cartRepository, UserRepository $userRepository, ProductRepository $productRepository): JsonResponse
    {
        try{
            $token = $authorization->extractTokenFromAuthorizationHeader($request,$logger);

            $logger->info("Décodage du token en cours");
            $decoded = JWT::decode($token, new Key($_ENV['SECRET_KEY'], 'HS256'));
            $userId = $decoded->userId;
            $user = $userRepository->getUserById($userId);

            $logger->info("Récupération des données via la base de données du panier");
            $products = $cartRepository->getProducts($user);
            $jsonData = [];

            for($i = 0; $i < count($products); $i++){
                $jsonData[$i] = [
                    'id' => $products[$i]->getId(),
                    'name' => $products[$i]->getName(),
                    'description' => $products[$i]->getDescription(),
                    'photo' => $products[$i]->getPhoto(),
                    'price' => $products[$i]->getPrice(),
                ];
            }

            return new JsonResponse($jsonData,200);

        }
        catch(Exception $e){
            $logger->error("Une erreur est survenue lors de la modification du produit.");
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

    #[Route('/api/carts/validate', name: 'api_cart_validation')]
    public function validateCart(LoggerInterface $logger, Request $request, AuthorizationController $authorization, CartRepository $cartRepository, UserRepository $userRepository, OrderRepository $orderRepository): JsonResponse
    {
        try{
            $token = $authorization->extractTokenFromAuthorizationHeader($request,$logger);

            $logger->info("Décodage du token en cours");
            $decoded = JWT::decode($token, new Key($_ENV['SECRET_KEY'], 'HS256'));
            $userId = $decoded->userId;
            $user = $userRepository->getUserById($userId);

            $newOrder = new Order();
            $newOrder->setUserId($userId);
            $products = $cartRepository->getProducts($user);

            $totalPrice = 0.00;

            $logger->info("Boucle de validation du panier");
            for($i = 0; $i < count($products); $i++){
                $products[$i]->setValidateOrder($newOrder);
                $totalPrice += $products[$i]->getPrice();
                $newOrder->addProduct($products[$i]);
            }

            $cartRepository->clearCart($user);

            $logger->info("Mise à jour du prix total de la commande");
            $newOrder->setTotalPrice($totalPrice);
            $newOrder->setCreationDate(date("Y-m-d H:i:s"));

            $logger->info("Sauvegarde de la commande dans la base de données");
            $orderRepository->addOrder($newOrder);

            return new JsonResponse(["statut" => "Le panier a bien été validé"],201);

        }
        catch(Exception $e){
            $logger->error("Une erreur est survenue lors de la modification du produit.");
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
