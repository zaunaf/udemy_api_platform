<?php

namespace App\EventSubscriber;

Use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use ApiPlatform\Core\EventListener\EventPriorities;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use App\Entity\AuthoredEntityInterface;
use App\Entity\PublishedDateEntityInterface;
use DateTime;

class PublishedDateEntitySubscriber implements EventSubscriberInterface 
{
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['setDatePublished', EventPriorities::PRE_WRITE]
        ];
    }

    public function setDatePublished(GetResponseForControllerResultEvent $event)
    {
        $entity = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();
        
        if (!$entity instanceof PublishedDateEntityInterface || Request::METHOD_POST !== $method) {            
            return;
        }

        $entity->setPublished(new \DateTime());
    }
}