<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    #[Route('/api/carts/{productId}', name: 'api_add_product_cart')]
    public function addCartProduct(): JsonResponse
    {
        //todo
    }

    #[Route('/api/carts/{productId}', name: 'api_remove_product_cart')]
    public function removeCartProduct(): JsonResponse
    {
        //todo
    }

    #[Route('/api/carts', name: 'api_cart')]
    public function displayCart(): JsonResponse
    {
        //todo
    }

    #[Route('/api/carts/validate', name: 'api_cart_validation')]
    public function validateCart(): JsonResponse
    {
        //todo
    }
}
