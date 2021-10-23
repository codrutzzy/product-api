<?php

namespace App\Controller;

use App\Entity\OrderItem;
use App\Repository\ProductRepository;
use App\Manager\CartManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController
{
    private $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * @Route("/product/", name="add_product", methods={"POST"})
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function add(Request $request): JsonResponse
    {
        $data = \json_decode($request->getContent(), true);

        $name = $data['name'] ?? '';
        $description = $data['description'] ?? '';
        $price = $data['price'] ?? '';

        if (empty($name) || empty($price)) {
            return new JsonResponse(['status' => 'Expecting mandatory parameters!'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $this->productRepository->saveProduct($name, $description, $price);

        return new JsonResponse(['status' => 'Product created!'], Response::HTTP_CREATED);
    }

    /**
     * @Route("/product/{id}", name="get_one_product", methods={"GET"})
     *
     * @param int $id
     * @return JsonResponse
     */
    public function get(int $id): JsonResponse
    {
        $product = $this->productRepository->findOneBy(['id' => $id]);
        if ($product !== null) {
            return new JsonResponse($product->toArray(), Response::HTTP_OK);
        }


        return new JsonResponse([], Response::HTTP_NOT_FOUND);
    }

    /**
     * @Route("/product", name="get_all_product", methods={"GET"})
     */
    public function getAll(): JsonResponse
    {
        $products = $this->productRepository->findAll();
        $data = [];

        foreach ($products as $product) {
            $data[] = $product->toArray();
        }

        return new JsonResponse($data, Response::HTTP_OK);
    }

    /**
     * @Route("/product/{id}", name="update_product", methods={"PUT"})
     * @param int $id
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function update(int $id, Request $request): JsonResponse
    {
        $product = $this->productRepository->findOneBy(['id' => $id]);

        if (empty($product)) {
            return new JsonResponse(['status' => 'Product not found'], Response::HTTP_NOT_FOUND);
        }

        $data = \json_decode($request->getContent(), true);

        empty($data['description']) ? true : $product->setName($data['description']);
        empty($data['name']) ? true : $product->setName($data['name']);
        empty($data['price']) ? true : $product->setPrice($data['price']);

        $updatedProduct = $this->productRepository->updateProduct($product);

        return new JsonResponse($updatedProduct->toArray(), Response::HTTP_OK);
    }

    /**
     * @Route("/product/{id}", name="delete_product", methods={"DELETE"})
     *
     * @param int $id
     *
     * @return JsonResponse
     */
    public function delete(int $id): JsonResponse
    {
        $product = $this->productRepository->findOneBy(['id' => $id]);

        if ($product !== null) {
            $this->productRepository->removeProduct($product);

            return new JsonResponse(['status' => 'Product deleted'], Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(['status' => 'Product does not exist'], Response::HTTP_NO_CONTENT);
    }

    /**
     * @Route("/addToCart/{id}/{qty}", name="add_to_cart_product", methods={"GET"})
     *
     * @param int $id
     * @param int $qty
     * @param CartManager $cartManager
     *
     * @return JsonResponse
     */
    public function addToCart(int $id, int $qty, CartManager $cartManager): JsonResponse
    {
        $product = $this->productRepository->findOneBy(['id' => $id]);
        $item = new OrderItem();
        $item->setProduct($product);
        $item->setQuantity($qty);
        $item->setProductPrice($product->getPrice());

        $cart = $cartManager->getCurrentCart();
        $cart
            ->addItem($item)
            ->setUpdatedAt(new \DateTime());

        $cartManager->save($cart);

        return new JsonResponse(['status' => 'Product added to cart'], Response::HTTP_NO_CONTENT);
    }
}
