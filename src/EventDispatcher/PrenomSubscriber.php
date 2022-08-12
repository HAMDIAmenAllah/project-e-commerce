<?php

namespace App\EventDispatcher;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class PrenomSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            'kernel.request' => 'addPrenomToAttributes',
            'kernel.controller' => 'teste1',
            'kernel.response' => 'teste2'
        ];
    }

    public function addPrenomToAttributes(RequestEvent $requestEvent)
    {
        $requestEvent->getRequest()->attributes->set('prenom', 'Amen');
        // dd($requestEvent->getRequest());
    }

    public function teste1()
    {
        // dump('teste1');
    }
    public function teste2()
    {
        // dump('teste2');
    }
}
