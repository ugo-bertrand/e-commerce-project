<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Log\LoggerInterface;
use App\Entity\Product;
use App\Repository\ProductRepository;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use App\Controller\JsonErrorController;
use App\Exception\ForbiddenRequestException;
use App\Exception\NotFoundException;
use App\Exception\UnauthorizedException;
use DateTimeImmutable;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class ProductController extends AbstractController
{
    #[Route('/api/products', name: 'api_products')]
    public function listProducts(LoggerInterface $logger, ProductRepository $productRepository): JsonResponse
    {
        try{
            $products = $productRepository->getProducts();
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
            $logger->error("Une erreur est survenue lors de la récupération des données du produit");
            $error = new JsonErrorController();
            if($e->getCode() == 404){
                return $error->showError($e);
            }
            else{
                return $error->showDefaultError();
            }
        }
    }

    #[Route('/api/products/{productId}', name: 'api_products_id')]
    public function getProductById(LoggerInterface $logger, Request $request, ProductRepository $productRepository, int $productId): JsonResponse
    {
        try{
            $product = $productRepository->getProductById($productId);

            return new JsonResponse([
                'id' => $product->getId(),
                'name' => $product->getName(),
                'description' => $product->getDescription(),
                'photo' => $product->getPhoto(),
                'price' => $product->getPrice(),
            ],200);

        }
        catch(Exception $e){
            $logger->error("Une erreur est survenue lors de la récupération des données du produit");
            $error = new JsonErrorController();
            if($e->getCode() == 404){
                return $error->showError($e);
            }
            else{
                return $error->showDefaultError();
            }
        }
    }

    #[Route('/api/products', name: 'api_add_product')]
    public function createProduct(LoggerInterface $logger, Request $request, AuthorizationController $authorization, ProductRepository $productRepository): JsonResponse
    {
        try{
            $token = $authorization->extractTokenFromAuthorizationHeader($request,$logger);

            $logger->info("Décodage du token en cours");
            $decoded = JWT::decode($token, new Key($_ENV['SECRET_KEY'], 'HS256'));
            
            $logger->info("Début de la création d'un produit.");
            $bodyRequest = $request->toArray();
            $logger->info(count($bodyRequest));
            $logger->info($bodyRequest['name']);
            $logger->info($bodyRequest['description']);
            $logger->info($bodyRequest['photo']);
            $logger->info($bodyRequest['price']);
            if(count($bodyRequest) != 4){
                throw new NotFoundException("Le contenu ne doit pas être vide, veuillez mettre les valeurs correspondantes");
            }
            $logger->info("Récupération des données pour créer le produit");

            $newProduct = new Product();
            $newProduct->setName($bodyRequest['name']);
            $newProduct->setDescription($bodyRequest['description']);
            $newProduct->setPhoto($bodyRequest['photo']);
            $newProduct->setPrice($bodyRequest['price']);
            $productRepository->save($newProduct,true);

            return new JsonResponse([
                'message' => "Le produit a été créé"
            ],201);
        }
        catch(Exception $e){
            $logger->error("Une erreur est survenue");
            $error = new JsonErrorController();
            if($e->getCode() == 404){
                return $error->showError($e);
            }
            if($e->getCode() == 401){
                return $error->showError($e);
            }
            else{
                return $error->showDefaultError();
            }
        }
    }

    #[Route('/api/products/{productId}', name: 'api_update_product')]
    public function updateProduct(LoggerInterface $logger, Request $request, AuthorizationController $authorization, ProductRepository $productRepository, int $productId): JsonResponse
    {
        try{
            $token = $authorization->extractTokenFromAuthorizationHeader($request,$logger);

            $logger->info("Décodage du token en cours");
            $decoded = JWT::decode($token, new Key($_ENV['SECRET_KEY'], 'HS256'));

            $logger->info("Récupération des données à modifier pour le produit");
            $bodyRequest = $request->toArray();

            if(count($bodyRequest) != 4){
                throw new NotFoundException("Le contenu ne doit pas être vide, veuillez mettre les valeurs correspondantes");
            }

            $logger->info("Récupération des données via la base de données du produit");
            $newProduct = new Product();
            $newProduct->setName($bodyRequest['name']);
            $newProduct->setDescription($bodyRequest['description']);
            $newProduct->setPhoto($bodyRequest['photo']);
            $newProduct->setPrice($bodyRequest['price']);

            $logger->info("Enregistrement des nouvelles données dans la base de données");

            $productRepository->updateProductById($productId,$newProduct);

            return new JsonResponse([
                "statut" => "Le produit a bien été modifié"
            ],200);

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

    #[Route('/api/products/{productId}', name: 'api_delete_product')]
    public function deleteProduct(LoggerInterface $logger, Request $request, AuthorizationController $authorization, ProductRepository $productRepository, int $productId): JsonResponse
    {
        try{
            $token = $authorization->extractTokenFromAuthorizationHeader($request,$logger);
            $logger->info("Décodage du token en cours");
            $decoded = JWT::decode($token, new Key($_ENV['SECRET_KEY'],'HS256'));
            $logger->info("decodage ok");
            $productRepository->deleteProductById($productId);

            return new JsonResponse([
                "statut" => "Le produit a bien été supprimé"
            ],200);
        }
        catch(Exception $e){
            $logger->error("Une erreur est survenue lors de la récupération des données du produit");
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
