<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    #[Route('/api/products', name: 'api_products')]
    public function listProducts(): JsonResponse
    {
        //todo
    }

    #[Route('/api/products/{productId}', name: 'api_products_id')]
    public function getProductById(): JsonResponse
    {
        //todo
    }

    #[Route('/api/products', name: 'api_add_product')]
    public function createProduct(): JsonResponse
    {
        //todo
    }

    #[Route('/api/products/{productId}', name: 'api_update_product')]
    public function updateProduct(): JsonResponse
    {
        //todo
    }

    #[Route('/api/products/{productId}', name: 'api_delete_product')]
    public function deleteProduct(): JsonResponse
    {
        //todo
    }
}
