<?php

namespace CoderDojo\WebsiteBundle\Twig;

use CoderDojo\WebsiteBundle\Entity\Dojo;
use CoderDojo\WebsiteBundle\Entity\DojoEvent;

class DojoExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('nextEvent', array($this, 'nextEventFilter'))
        );
    }

    /**
     * @param Dojo $dojo
     * @return DojoEvent|null
     */
    public function nextEventFilter(Dojo $dojo)
    {
        $events = $dojo->getEvents();

        if ($events->isEmpty()) {
            return null;
        }

        $events = array_filter($events->toArray(), function(DojoEvent $event){
            if ($event->getDate() >= new \DateTime()) {
                return true;
            }

            return false;
        });

        if (count($events) === 0) {
            return null;
        }

        usort($events, function(DojoEvent $a, DojoEvent $b){
            return $a->getDate() > $b->getDate();
        });

        return $events[0];
    }

    public function getName()
    {
        return 'dojo_extension';
    }
}