<?php

namespace App\Controller;

use App\Entity\Order;
use App\Manager\CartManager;
use App\Repository\OrderRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController
{
    private $orderRepository;

    public function __construct(OrderRepository $productRepository)
    {
        $this->orderRepository = $productRepository;
    }

    /**
     * @Route("/checkout/", name="add_order", methods={"POST"})
     *
     * @param Request $request
     * @param CartManager $cartManager
     *
     * @return JsonResponse
     */
    public function checkout(Request $request, CartManager $cartManager): JsonResponse
    {
        $data = \json_decode($request->getContent(), true);

        $email = $data['email'] ?? '';
        $cart = $cartManager->getCurrentCart();

        if (empty($email) || ($cart === null)) {
            return new JsonResponse(['status' => 'Expecting mandatory parameters!'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $cart->setUpdatedAt(new \DateTime());
        $cart->setStatus(Order::STATUS_FINALISED);
        $cart->setEmail($email);
        $cartManager->save($cart, true);

        return new JsonResponse(['status' => 'Order created!'], Response::HTTP_CREATED);
    }

    /**
     * @Route("/listOrders/{startDate}/{endDate}", name="list_order", methods={"GET"})
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function listOrders(string $startDate, string $endDate): JsonResponse
    {
        $fromDate = \DateTime::createFromFormat('Y-m-d-H-i-s', $startDate);
        $toDate = \DateTime::createFromFormat('Y-m-d-H-i-s', $endDate);

        $orders = $this->orderRepository->listOrders($fromDate, $toDate) ?? [];

        foreach ($orders as $order) {
            $orderResponse[] = $order->toArray();
        }

        return new JsonResponse($orderResponse ?? [], Response::HTTP_OK);
    }
}
