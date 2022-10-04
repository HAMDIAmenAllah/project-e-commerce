<?php

namespace App\Controller\Purchase;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Cart\CartService;
use App\Entity\Purchase;
use App\Entity\PurchaseItem;
use App\Form\CartConfirmationType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;

class PuchaseConfirmationController_with_service extends AbstractController
{
    protected $em;
    protected $formFactroy;
    protected $router;
    protected $security;
    protected $cartService;
    public function __construct(FormFactoryInterface $formFactoryInterface, RouterInterface $router, Security $security, CartService $cartService, EntityManagerInterface $em)
    {
        $this->formFactroy = $formFactoryInterface;
        $this->router = $router;
        $this->security = $security;
        $this->cartService = $cartService;
        $this->em = $em;
    }
    /**
     * 
     */
    public function confirm(Request $request, FlashBagInterface $flashBag)
    {
        // 1. Lire les données du formulaire (formFactoryInterface / request)

        $form = $this->formFactroy->create(CartConfirmationType::class);

        $form->handleRequest($request);


        // 2. Si le formulaire n'a pas été soumis : dégager


        if (!$form->isSubmitted()) {
            $flashBag->add('warning', 'Vous devez remplir le formulaire de confirmation');
            return new RedirectResponse($this->router->generate('cart_show'));
        }
        // 3. Si je ne suis pas connécté : dégager (sécurity)

        $user = $this->security->getUser();
        if (!$user) {
            throw new AccessDeniedException("Vous devez être connecté pour confirmer une commande");
        }

        // 4. Si il n'y a pas de produits dans mon panier : dégager (cartservice)

        $cartItems = $this->cartService->getDetailedCartItems();

        if (count($cartItems) === 0) {

            $flashBag->add('warning', 'Vous ne pouvez pas confirmer une commande avec un panier vide');
            return new RedirectResponse($this->router->generate('cart_show'));
        }


        // 5. Créer la purchase
        /**
         * @var Purchase
         */
        $purchase = $form->getData();

        // 6. Lier la purchase avec l'utilisateur actuellement connecté (sécurity)

        $purchase->setUser($user)
            ->setPurchasedAt(new DateTime());
        $this->em->persist($purchase);
        // 7. Lier la purchase avec les produits qui sont dans le panier (cartservice)
        $total = 0;
        foreach ($this->cartService->getDetailedCartItems() as $cartItems) {
            $purchaseItem = new PurchaseItem;
            $purchaseItem->setPurchase($purchase)
                ->setProduct($cartItems->product)
                ->setProductName($cartItems->product->getName())
                ->setQuantity($cartItems->qty)
                ->setTotal($cartItems->getTotal())
                ->setProductPrice($cartItems->product->getPrice());

            $total += $this->cartService->getTotal();

            $this->em->persist($purchaseItem);
        }
        $purchase->setTotal($total);
        // 8. enregistrer la commande (EntityMAnagerInterface)

        $this->em->flush();

        $flashBag->add('success', 'La commande a été enregistrée');

        return new RedirectResponse($this->router->generate('purchases_index'));
    }
}
