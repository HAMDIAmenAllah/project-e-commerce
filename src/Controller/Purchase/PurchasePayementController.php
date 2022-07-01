<?php

namespace App\Controller\Purchase;

use App\Entity\Purchase;
use App\Repository\PurchaseRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PurchasePayementController extends AbstractController
{
    /**
     * @Route("/purchase/pay/{id}",name="purchase_payement_form")
     */
    public function showCartForm($id, PurchaseRepository $purchaseRepository)
    {
        $purchase = $purchaseRepository->find($id);
        if (!$purchase) {
            return $this->redirectToRoute('cart_show');
        }
        \Stripe\Stripe::setApiKey('sk_test_51LGKkFCm4wUOY6JaL9eOmPwiJ9qqO42kBzSfF9Fl7AUG1ck1LGaVRYiCcUmFiFPQ5QmqDlwJD9Sx8cQbJMLCcy5i00gXRCdSJo');
        $intent = \Stripe\PaymentIntent::create([
            'amount' => $purchase->getTotal(),
            'currency' => 'eur'
        ]);

        return $this->render('purchases/payment.html.twig', [
            'clientSecret' => $intent->client_secret
        ]);
    }
}
