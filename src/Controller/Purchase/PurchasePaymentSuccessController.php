<?php

namespace App\Controller\Purchase;

use App\Entity\Purchase;
use App\Cart\CartService;
use App\Event\PurchaseSuccessEvent;
use App\Repository\PurchaseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class PurchasePaymentSuccessController extends AbstractController
{
    /**
     * @Route("/purchase/terminate/{id}", name="purchase_payment_success")
     * @IsGranted("ROLE_USER")
     */
    public function success($id, PurchaseRepository $purchaseRepository, EntityManagerInterface $em, CartService $cartService, EventDispatcherInterface $dispatcher): Response
    {
        // 1. récupérer la commande
        $purchase = $purchaseRepository->find($id);
        if (
            !$purchase ||
            ($purchase && $purchase->getUser() !== $this->getUser()) ||
            ($purchase && $purchase->getStatus() === Purchase::STATUS_PAID)
        ) {
            $this->addFlash('warning', "la commande n'existe pas");
            return $this->redirectToRoute('purchases_index');
        }
        // 2. Passer au statut payée
        $purchase->setStatus(Purchase::STATUS_PAID);
        $em->flush();
        // 3. Je vide le panier
        $cartService->empty();

        // lancer un évenement qui permette aux autres développeurs de réagir à la prise d'une commande
        $purchaseEvent = new PurchaseSuccessEvent($purchase);
        $dispatcher->dispatch($purchaseEvent, 'purchase.success');
        // 4. je redirige avec un flash vers la liste des commandes
        $this->addFlash('success', "La commande a été payée et confirmée !");
        return $this->redirectToRoute('purchases_index');
    }
}
