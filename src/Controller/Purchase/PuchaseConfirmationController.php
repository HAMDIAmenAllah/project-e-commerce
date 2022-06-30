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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;

class PuchaseConfirmationController extends AbstractController
{
    protected $em;
    protected $cartService;
    public function __construct(Security $security, CartService $cartService, EntityManagerInterface $em)
    {

        $this->cartService = $cartService;
        $this->em = $em;
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

        // 6. Lier la purchase avec l'utilisateur actuellement connecté (sécurity)

        $purchase->setUser($user)
            ->setPurchasedAt(new DateTime())
            ->setTotal($this->cartService->getTotal());
        $this->em->persist($purchase);
        // 7. Lier la purchase avec les produits qui sont dans le panier (cartservice)
        foreach ($this->cartService->getDetailedCartItems() as $cartItems) {
            $purchaseItem = new PurchaseItem;
            $purchaseItem->setPurchase($purchase)
                ->setProduct($cartItems->product)
                ->setProductName($cartItems->product->getName())
                ->setQuantity($cartItems->qty)
                ->setTotal($cartItems->getTotal())
                ->setProductPrice($cartItems->product->getPrice());


            $this->em->persist($purchaseItem);
        }
        // 8. enregistrer la commande (EntityMAnagerInterface)

        $this->em->flush();

        $this->cartService->empty();

        $this->addFlash('success', 'La commande a été enregistrée');

        return $this->redirectToRoute('purchases_index');
    }
}
