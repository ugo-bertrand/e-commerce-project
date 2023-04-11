<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    #[Route('/api/orders/', name: 'app_order_user')]
    public function findAllOrder(): JsonResponse
    {
        //todo
    }

    #[Route('/api/orders/{orderId}', name: 'app_order_id')]
    public function findOrderById(): JsonResponse
    {
        //todo
    }

}
