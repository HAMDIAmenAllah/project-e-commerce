<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function index(ProductRepository $productRepository): Response
    {
        dump($productRepository->findBy([], [], 3));
        $product = $productRepository->findBy([], [], 3);

        // $product = new Product();
        // $product
        //     ->setName('chaise en bois')
        //     ->setPrice(2000)
        //     ->setSlug('chaise-en-bois');

        // $em->persist($product);
        // $em->flush();

        return $this->render('home.html.twig', [
            'controller_name' => 'HomeController',
            'products' => $product,
        ]);
    }
}
