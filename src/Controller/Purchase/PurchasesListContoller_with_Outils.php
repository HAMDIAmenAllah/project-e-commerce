<?php

namespace App\Controller\Purchase;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;
use Twig\Environment;

class PurchasesListContoller_with_Outils extends AbstractController
{
    protected $security;
    protected $router;
    protected $twig;

    public function __construct(Security $security, RouterInterface $router, Environment $twig)
    {
        $this->security = $security;
        $this->router = $router;
        $this->twig = $twig;
    }
    // classe d'exemple avec l'utilisation des outils
    public function index(): Response
    {
        /**
         * @var User
         */

        // 1. Nous devons nous assurer que la personne est connectée (sinon redirection vers la page d'accueil) 
        // Les outils utilisés ->Security.
        $user = $this->getUser();
        if (!$user) {
            // Générer une URL en fonction du nom d'une route
            // Les outils utilisés -> UrlGeneratorInterface ou RouterInterface
            /* $url = $this->router->generate('homepage');
            return new RedirectResponse($url); */
            $this->addFlash('danger', "Vous devez être connecté pour acceder à vos commandes");
            throw new AccessDeniedException("Vous devez être connecté pour acceder à vos commandes");
        }
        // 2. Nous voulons savoir qui est connecté.

        // Les outils utilisés ->Security.
        // 3. Nous voulons passer l'utilisateur connecté à twig afin d'afficher ces commandes. 
        // Les outils utilisés ->Environnement de twig / Response.
        // return $this->render('purchase/purchases_list_contoller/index.html.twig', [
        //     'controller_name' => 'PurchasesListContollerController',
        // ]);
        $html = $this->twig->render('purchases/index.html.twig', [
            'purchases' => $user->getPurchases(),
        ]);

        return new Response($html);
    }
}
