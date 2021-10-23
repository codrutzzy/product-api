<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct
    (
        ManagerRegistry $registry,
        EntityManagerInterface $manager
    )
    {
        parent::__construct($registry, Product::class);
        $this->manager = $manager;
    }

    /**
     * @param $name
     * @param $description
     * @param $price
     */
    public function saveProduct($name, $description, $price)
    {
        $newProduct = new Product();

        $newProduct
            ->setName($name)
            ->setDescription($description)
            ->setPrice($price);

        $this->manager->persist($newProduct);
        $this->manager->flush();
    }

    /**
     * @param Product $products
     *
     * @return Product
     */
    public function updateProduct(Product $product): Product
    {
        $this->manager->persist($product);
        $this->manager->flush();

        return $product;
    }

    /**
     * @param Product $product
     */
    public function removeProduct(Product $product)
    {
        $this->manager->remove($product);
        $this->manager->flush();
    }
}
