<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PurchasePaymentSuccessController extends AbstractController
{
    /**
     * @Route("/purchase/payment/success", name="app_purchase_payment_success")
     */
    public function index(): Response
    {
        return $this->render('purchase_payment_success/index.html.twig', [
            'controller_name' => 'PurchasePaymentSuccessController',
        ]);
    }
}
