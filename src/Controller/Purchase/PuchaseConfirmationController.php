<?php

namespace App\Controller\Purchase;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Cart\CartService;
use App\Entity\Purchase;
use App\Form\CartConfirmationType;
use App\Purchase\PurchasePersister;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class PuchaseConfirmationController extends AbstractController
{
    protected $em;
    protected $cartService;
    protected $persister;
    public function __construct(CartService $cartService, PurchasePersister $persister)
    {
        $this->cartService = $cartService;
        $this->persister = $persister;
    }
    /**
     * @Route("/purchase/confirm", name="purchase_confirm")
     */
    public function confirm(Request $request, FlashBagInterface $flashBag)
    {
        // 1. Lire les données du formulaire (formFactoryInterface / request)

        $form = $this->createForm(CartConfirmationType::class);

        $form->handleRequest($request);


        // 2. Si le formulaire n'a pas été soumis : dégager


        if (!$form->isSubmitted()) {
            $this->addFlash('warning', 'Vous devez remplir le formulaire de confirmation');
            return $this->redirectToRoute('cart_show');
        }
        // 3. Si je ne suis pas connécté : dégager (sécurity)

        $user = $this->getUser();
        if (!$user) {
            throw new AccessDeniedException("Vous devez être connecté pour confirmer uen commande");
        }

        // 4. Si il n'y a pas de produits dans mon panier : dégager (cartservice)

        $cartItems = $this->cartService->getDetailedCartItems();

        if (count($cartItems) === 0) {

            $this->addFlash('warning', 'Vous ne pouvez pas confirmer une commande avec un panier vide');
            return $this->redirectToRoute('cart_show');
        }

        // 5. Céer la purchase
        /**
         * @var Purchase
         */
        $purchase = $form->getData();
        // La suite des étapes sont dans la classe PurchasePersister d'où l'appel à cette ligne "$this->persister->storePurchase($purchase);"
        $this->persister->storePurchase($purchase);
        // supprimer le panier après le passage de ma session
        /* $this->cartService->empty();

        $this->addFlash('success', 'La commande a été enregistrée'); */

        return $this->redirectToRoute('purchase_payement_form', ['id' => $purchase->getId()]);
    }
}
