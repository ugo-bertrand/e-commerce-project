<?php

namespace App\Controller;

use App\Exception\ForbiddenRequestException;
use App\Exception\NotFoundException;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    #[Route('/api/orders/', name: 'app_order_user')]
    public function findAllOrder(LoggerInterface $logger, Request $request, OrderRepository $orderRepository, AuthorizationController $authorizationController, ProductRepository $productRepository): JsonResponse
    {
        try{
            $logger->info("Décodage du token en cours");
            $token = $authorizationController->extractTokenFromAuthorizationHeader($request, $logger);
            $decode = JWT::decode($token, new Key($_ENV['SECRET_KEY'], 'HS256'));
            $userId = $decode->userId;

            $logger->debug("Récupération de l'id de l'utilisateur {userId}", ['userId' => $userId]);

            $logger->info("Récupération des commandes de l'utilisateur");
            $listOfOrder = $orderRepository->getOrderOfUser($userId);
            $logger->debug("Affichage de la liste des commandes {list}", ['list' => $listOfOrder]);
            if($listOfOrder == null || count($listOfOrder) == 0){
                $logger->error("La liste de commandes est vide");
                throw new NotFoundException("Il n'y a pas de commandes pour cet utilisateur");
            }
            $logger->info("la liste des commandes a été récupérer");
            $jsonData = [];
            for($i = 0; $i < count($listOfOrder); $i++){
                $productData = [];
                $listOfProductOrder = $productRepository->findBy(['validateOrder' => $listOfOrder[$i]]);
                $logger->debug("liste des produits de la commande en cours {list}", ['list' => $listOfProductOrder[0]]);
                for($j = 0; $j < count($listOfProductOrder); $j++){
                    $productData[$j] = [
                        'id' => $listOfProductOrder[$j]->getId(),
                        'name' => $listOfProductOrder[$j]->getName(),
                        'description' => $listOfProductOrder[$j]->getDescription(),
                        'photo' => $listOfProductOrder[$j]->getPhoto(),
                        'price' => $listOfProductOrder[$j]->getPrice(),
                    ];   
                }
                $jsonData[$i] = [
                    'id' => $listOfOrder[$i]['id'],
                    'totalPrice' => $listOfOrder[$i]['total_price'],
                    'creationDate' => $listOfOrder[$i]['creation_date'],
                    'products' => $productData
                ];
            }
            $logger->info("Récupération des données réussie");
            return new JsonResponse($jsonData,200);
        }
        catch(Exception $e){
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

    #[Route('/api/orders/{orderId}', name: 'app_order_id')]
    public function findOrderById(LoggerInterface $logger, Request $request, OrderRepository $orderRepository, int $orderId, AuthorizationController $authorizationController): JsonResponse
    {
        try{
            $logger->info("Décodage du token en cours");
            $token = $authorizationController->extractTokenFromAuthorizationHeader($request,$logger);
            $decode = JWT::decode($token, new Key($_ENV['SECRET_KEY'], 'HS256'));
            $userId = $decode->userId;

            $logger->debug("Récupération de l'id de l'utilisateur {userId}", ['userId' => $userId]);
            $logger->info("Récupération de la commande avec l'id correspondant");
            $order = $orderRepository->find($orderId);
            if($order == null){
                $logger->error("La commande n'existe pas");
                throw new NotFoundException("La commande n'existe pas");
            }

            $logger->info("Vérification que l'utilisateur est autorisé à voir ces données");
            if($order->getUserId() != $userId){
                $logger->error("L'utilisateur n'est pas autorisé à voir ces données");
                throw new ForbiddenRequestException("L'utilisateur n'est pas autorisé à voir ces données");
            }

            $logger->info("Récupération et affichage des données");
            
            $productList = $order->getProducts();
            $productData = [];
            for($i = 0; $i < count($productList); $i++){
                $productData[$i] = [
                    'name' => $productList[$i]->getName(),
                    'description' => $productList[$i]->getDescription(),
                    'photo' => $productList[$i]->getPhoto(),
                    'price' => $productList[$i]->getPrice()
                ];
            }
            return new JsonResponse([
                'id' => $order->getId(),
                'totalPrice' => $order->getTotalPrice(),
                'creationDate' => $order->getCreationDate(),
                'products' => $productData
            ],200);
        }
        catch(Exception $e){
            $logger->error("Une erreur est survenue");
            $error = new JsonErrorController();
            if($e->getCode() == 401){
                return $error->showError($e);
            }
            else if($e->getCode() == 404){
                return $error->showError($e);
            }
            else if($e->getCode() == 403){
                return $error->showError($e);
            }
            else {
                return $error->showDefaultError();
            } 
        }
    }

}
