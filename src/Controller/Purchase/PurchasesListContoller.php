<?php

namespace App\Controller\Purchase;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class PurchasesListContoller extends AbstractController
{
    /**
     * @Route("/purchases", name="purchases_index")
     */
    public function index(): Response
    {
        /**
         * @var User
         */
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('danger', "Vous devez être connecté pour acceder à vos commandes");
            throw new AccessDeniedException("Vous devez être connecté pour acceder à vos commandes");
        }

        return $this->render('purchases/index.html.twig', [
            'purchases' => $user->getPurchases(),
        ]);
    }
}
