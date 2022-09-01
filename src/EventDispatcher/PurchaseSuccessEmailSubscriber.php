<?php

namespace App\EventDispatcher;

use App\Event\PurchaseSuccessEvent;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class PurchaseSuccessEmailSubscriber implements EventSubscriberInterface
{
    protected $mailer;
    protected $logger;
    public function __construct(LoggerInterface $logger, MailerInterface $mailer)
    {
        $this->mailer = $mailer;
        $this->logger = $logger;
    }
    public static function getSubscribedEvents()
    {
        return [
            'purchase.success' => 'sendSuccessEmail'
        ];
    }
    public function sendSuccessEmail(PurchaseSuccessEvent $purchaseSuccessEvent)
    {
        $currentUser = $purchaseSuccessEvent->getpurchase()->getUser();
        $purchase = $purchaseSuccessEvent->getpurchase();
        $email = new TemplatedEmail();
        $email->to(new Address($currentUser->getEmail(), $currentUser->getFullName()))
            ->from("contact@mail.com")
            ->subject("Bravo, votre commande n° {$purchase->getId()} a été bien confirmée")
            ->htmlTemplate("emails/purchase_success.html.twig")
            ->context([
                'purchase' => $purchase,
                'user' => $currentUser
            ]);
        $this->mailer->send($email);
        // $this->logger->info('Email envoyé pour la commande n° ' . $purchaseSuccessEvent->getpurchase()->getId());
    }
}
