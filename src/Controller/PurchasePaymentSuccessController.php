<?php

namespace App\Controller;

use App\Entity\Purchase;
use App\Event\PurchaseSuccessEvent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PurchasePaymentSuccessController extends AbstractController
{
    /**
     * @Route("/purchase/payment/success", name="app_purchase_payment_success")
     */
    public function index(EventDispatcherInterface $dispatcher, Purchase $purchase): Response
    {
        // $purchaseEvent = new PurchaseSuccessEvent($purchase);
        // $dispatcher->dispatch($purchaseEvent, 'purchase.success');
        return $this->render('purchase_payment_success/index.html.twig', [
            'controller_name' => 'PurchasePaymentSuccessController',
        ]);
    }
}
