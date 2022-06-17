<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CategoryController extends AbstractController
{
    /*     protected $categoryRepository;
    public function __construct($categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function renderMenuList(): Response
    {
        $categories = $this->categoryRepository->findAll();
        return $this->render('category/_menu.html.twig', [
            'categories' => $categories,
        ]);
    } */

    /**
     * @Route("/category", name="app_category")
     */
    public function index(): Response
    {
        return $this->render('category/index.html.twig', [
            'controller_name' => 'CategoryController',
        ]);
    }

    /**
     * @Route("/admin/category/create", name="category_create")
     */
    public function create(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $category->setSlug(strtolower($slugger->slug($category->getName())));
            $em->persist($category);
            $em->flush();

            return $this->redirectToRoute('homepage');
        }
        $formView = $form->createView();
        $category = new Category();
        return $this->render('category/create.html.twig', [
            'formView' => $formView,
        ]);
    }

    /**
     * @Route("/admin/category/{id}/edit", name="category_edit")
    
     */
    // mettre is granted dans l'annotationça dans l'annotation pour gérer les rôles 
    // @IsGranted("ROLE_ADMIN", message="Vous n'avez pas le droit d'acceder à cette resource")
    public function edit($id, CategoryRepository $categoryRepository, Request $request, EntityManagerInterface $em, SluggerInterface $slugger, Security $security): Response
    {

        // $this->denyAccessUnlessGranted("ROLE_ADMIN", null, "Vous n'avez pas le droit d'acceder à cette resource");
        // $user = $security->getUser();
        // if ($user === null) {
        //     return $this->redirectToRoute('security_login');
        // }

        // if (!in_array("ROLE_ADMIN", $user->getRoles())) {
        //     throw new AccessDeniedHttpException("Vous n'avez pas le droit d'acceder à cette ressource");
        // }

        $category = $categoryRepository->find($id);

        if (!$category) {
            throw new NotFoundHttpException("Cette catégorie n'existe pas");
        }
        /* //début code 0A
        //récupérer l'utilisateur
        $user = $this->getUser();
        // Rediriger su personne n'est connécté
        if (!$user) {
            return $this->redirectToRoute('security_login');
        }
        // Vérifier si c'est le rateur de la catégorie
        if ($user !== $category->getOwner()) {
            throw new AccessDeniedHttpException("vous n'êtes pas le propiétaire de cette catégorie");
        }
        // fin code 0A */

        /* // on remplace le code 0A par le voter la ligne ci desous
        //début voter
        // $security->isGranted('CAN_EDIT', $category);
        //ou
        $this->denyAccessUnlessGranted('CAN_EDIT', $category, "Vous n'êtes pas le propiètaire de cette catégorie");
        //fin voter */

        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $category->setSlug(strtolower($slugger->slug($category->getName())));
            $em->flush();

            return $this->redirectToRoute('homepage');
        }
        $formView = $form->createView();
        return $this->render('category/edit.html.twig', [
            'category' => $category,
            'formView' => $formView
        ]);
    }
}
