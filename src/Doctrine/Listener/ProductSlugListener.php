<?php

namespace App\Doctrine\Listener;

use App\Entity\Product;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProductSlugListener
{
    protected $slugger;
    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }
    /* fonction avec les config générals dans yaml
    public function prePersist(LifecycleEventArgs $event)
    {
        $entity = $event->getObject();
        if (!$entity instanceof Product) {
            return;
        }

        if (empty($entity->getSlug())) {
            $entity->setSlug(strtolower($this->slugger->slug($entity->getName())));
        }
    } */

    // fonction avec les config sur une entity spécifique dans yaml
    // le product slugListener sera appelé uniquement au prepersist de mon entity product et pas au autres.
    public function prePersist(Product $entity)
    {
        if (empty($entity->getSlug())) {
            $entity->setSlug(strtolower($this->slugger->slug($entity->getName())));
        }
    }
}
