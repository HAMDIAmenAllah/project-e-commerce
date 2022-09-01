<?php

namespace App\EventDispatcher;

use App\Event\ProductMailEvent;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class ProductMailSubscriber implements EventSubscriberInterface
{
    protected $logger;
    protected $mailer;

    public function __construct(LoggerInterface $logger, MailerInterface $mailer)
    {
        $this->logger = $logger;
        $this->mailer = $mailer;
    }
    public static function getSubscribedEvents()
    {
        return [
            'product.view' => 'sendMail'
        ];
    }

    public function sendMail(ProductMailEvent $productMailEvent)
    {
        // $email = new TemplatedEmail();
        // $email->from(new Address("contact@mail.com", "Infos de la boutique"))
        //     ->to("admin@mail.com")
        //     ->text("Un visiteur est en train de voir la page du produit n°" . $productMailEvent->getProduct()->getId())
        //     ->htmlTemplate("emails/product_view.html.twig")
        //     ->context(["product" => $productMailEvent->getProduct()])
        //     ->subject("Visite du produit n° " . $productMailEvent->getProduct()->getId());
        // $this->mailer->send($email);
        // $this->mailer->$this->logger->info('email envoyé à l\'admin concernant le produit n° ' . $productMailEvent->getProduct()->getId());
    }
}
