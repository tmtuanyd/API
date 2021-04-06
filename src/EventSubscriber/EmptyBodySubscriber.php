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
        return [KernelEvents::VIEW => ['handleEmptyBody', EventPriorities::PRE_VALIDATE]];
    }
    public function handleEmptyBody(ViewEvent $event)
    {
        $method = $event->getRequest()->getMethod();
        var_dump($method);
        if(!in_array($method, [Request::METHOD_POST, Request::METHOD_PUT])){
            return;
        }
//        $data = $event->getRequest()->get('data');
        $data = $event->getControllerResult();
        var_dump($data);
        if(null === $data)
        {
            throw new EmptyBodyException();
        }
    }
}
