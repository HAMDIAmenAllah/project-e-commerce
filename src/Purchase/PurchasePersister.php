<?php

namespace App\Purchase;

use App\Cart\CartService;
use App\Entity\Purchase;
use App\Entity\PurchaseItem;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class PurchasePersister
{
    protected $user;
    protected $cartService;

    public function __construct(Security $security, CartService $cartService, EntityManagerInterface $em)
    {
        $this->security = $security;
        $this->cartService = $cartService;
        $this->em = $em;
    }

    public function storePurchase(Purchase $purchase)
    {
        // 6. Lier la purchase avec l'utilisateur actuellement connecté (sécurity)

        $purchase->setUser($this->security->getUser());
            /* Ci desous remplacer par l'evennement call-back qui est dans l'entité Purchase :  public function prePersist() et public function preFlush()
            ->setPurchasedAt(new DateTime())
            ->setTotal($this->cartService->getTotal()); */
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
    }
}
