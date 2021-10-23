# php-api

This is a small api to play with Symfony5 and Docker.

## Installation


```bash
docker-compose up -d --build
```
Inside the php container you will need to run the following commands:

```bash
docker-exec...

composer install
bin/console doctrine:migrations:status
bin/console doctrine:migrations:migrate
bin/console doctrine:fixtures:load (optional to create some dummy products for testing purposes)

```

## Usage

Webservices can be accessed at http://localhost:8000

There will be the defauld CRUD ones related to products:

```bash
##Create

* @Route("/product/", name="add_product", methods={"POST"})

##Read

* @Route("/product/{id}", name="get_one_product", methods={"GET"})
* @Route("/product", name="get_all_product", methods={"GET"})

##Update

 * @Route("/product/{id}", name="update_product", methods={"PUT"})

##Delete

* @Route("/product/{id}", name="delete_product", methods={"DELETE"})

##Add Product to cart

 * @Route("/addToCart/{id}/{qty}", name="add_to_cart_product", methods={"GET"})

##Finalize order

* @Route("/checkout/", name="add_order", methods={"POST"})

## List Orders

* @Route("/listOrders/{startDate}/{endDate}", name="list_order", methods={"GET"})

```

//TODO
Use OAUTH 2.0 to secure the checkout process and to grant access to orders. 


