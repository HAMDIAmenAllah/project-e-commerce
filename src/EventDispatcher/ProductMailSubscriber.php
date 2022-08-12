<?php

namespace App\EventDispatcher;

use App\Event\ProductMailEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProductMailSubscriber implements EventSubscriberInterface
{
    protected $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
    public static function getSubscribedEvents()
    {
        return [
            'product.view' => 'sendMail'
        ];
    }

    public function sendMail(ProductMailEvent $productMailEvent)
    {
        $this->logger->info('email envoyé à l\'admin concernant le produit n° ' . $productMailEvent->getProduct()->getId());
    }
}
