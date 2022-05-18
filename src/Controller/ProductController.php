<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\MakerBundle\Validator;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProductController extends AbstractController
{
    /**
     * @Route("/{slug}", name="product_category")
     */
    public function category($slug, CategoryRepository $categoryRepository): Response
    {
        $category = $categoryRepository->findOneBy(['slug' => $slug]);
        // if (!$category) {
        //     throw new NotFoundHttpException("La catégorie demandée n'existe pas!");
        // }
        /*  if (!$category) {
            throw $this->createNotFoundException("La catégorie demandée n'existe pas!");
        } */
        return $this->render('product/category.html.twig', [
            'category' => $category
        ]);
    }

    /**
     * @Route("/{category_slug}/{slug}", name="product_show")
     */
    public function show($slug, ProductRepository $productRepository): Response
    {
        // dd($urlGenerator->generate('product_category',
        // ['slug'=>"slug-de-teste"]));
        $product = $productRepository->findOneBy(['slug' => $slug]);

        /*   if (!$product) {
            throw $this->createNotFoundException("le produit demandé n'existe pas!");
        } */

        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }

    /**
     * @Route("/admin/product/{id}/edit", name="product_edit")
     */
    public function edit($id, ProductRepository $productRepository, Request $request, EntityManagerInterface $em, SluggerInterface $slugger, ValidatorInterface $validator): Response
    {
        /* $product = new Product();

        $resultat = $validator->validate($product); */
        // $client = [
        //     'nom' => 'HAMDI',
        //     'prenom' => '',
        //     'voiture' => [
        //         'marque' => '',
        //         'couleur' => 'noir'
        //     ]
        // ];
        // $collection = new Assert\Collection([
        //     'nom' => new Assert\NotBlank(['message' => 'le nom ne doit pas être vide']),
        //     'prenom' => new Assert\NotBlank(['message' => 'le prénom ne doit pas être vide']),
        //     new Assert\Length([
        //         'min' => 3,
        //         'minMessage' => 'le prénom doit comporter au minimum 3 lettres'
        //     ]),
        //     'voiture' => new Assert\Collection([
        //         'marque' => new Assert\NotBlank(['message' => 'la marque est obligatoire']),
        //         'couleur' => new Assert\NotBlank(['message' => 'la couleur den la voiture est obligatoire'])
        //     ])
        // ]);
        // $resultat = $validator->validate([$client, $collection]);
        // $age = 116;
        // $resultat = $validator->validate($age, [
        //     new Assert\LessThanOrEqual([
        //         'value' => 90,
        //         'message' => "l'âge doit être inférieur à {{ compared_value }}, mais vous avez donné {{ value }}"
        //     ]),
        //     new Assert\GreaterThan([
        //         'value' => 0,
        //         'message' => "l'âge doit être supérieur à O"
        //     ])
        // ]);
        /*  if ($resultat->count() > 0) {
            dd("il y a des erreur", $resultat);
        }

        dd("tout va bien"); */
        // $product = new Product();
        // $resultat = $validator->validate($product, null, ["Default", "group-price"]);
        // dd($resultat);
        $product = $productRepository->find($id);
        // $form = $this->createForm(ProductType::class, $product, [
        //     "validation_groups" => ["large-name", "group-price"]
        // ]);
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // dd($form->getData());
            $product->setSlug(strtolower($slugger->slug($product->getName())));
            $em->flush();

            return $this->redirectToRoute('product_show', [
                'category_slug' => $product->getCategory()->getSlug(),
                'slug' => $product->getSlug()
            ]);
        }
        $formView = $form->createView();

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'formView' => $formView
        ]);
    }

    /**
     * @Route("/admin/product/create", name="product_create")
     */
    public function create(Request $request, SluggerInterface $slugger, EntityManagerInterface $em): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        // $builder->setMethod('GET')
        //     ->setAction('/toto');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // $product = $form->getData();
            $product->setSlug(strtolower($slugger->slug($product->getName())));
            $em->persist($product);
            $em->flush();
            return $this->redirectToRoute('product_show', [
                'category_slug' => $product->getCategory()->getSlug(),
                'slug' => $product->getSlug()
            ]);
        }
        $formView = $form->createView();
        return $this->render('product/create.html.twig', [
            'formView' => $formView
        ]);
    }
}
