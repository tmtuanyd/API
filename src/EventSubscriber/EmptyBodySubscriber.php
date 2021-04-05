<?php


namespace App\EventSubscriber;


use ApiPlatform\Core\EventListener\EventPriorities;


use App\Exception\EmptyBodyException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class EmptyBodySubscriber implements EventSubscriberInterface
{

    public static function getSubscribedEvents()
    {
        // TODO: Implement getSubscribedEvents() method.
        return [KernelEvents::REQUEST => ['handleEmptyBody', EventPriorities::POST_DESERIALIZE]];
    }
    public function handleEmptyBody(RequestEvent $event)
    {
        $method = $event->getRequest()->getMethod();
        if(!in_array($method, [Request::METHOD_POST, Request::METHOD_PUT])){
            return;
        }
        $data = $event->getRequest()->get('data');
//        $data = $event->getResponse()->getContent();
        var_dump($data);
//        var_dump(null === $data);
        if(null === $data)
        {
            throw new EmptyBodyException();
        }
    }
}