<?php

namespace CoderDojo\WebsiteBundle\Twig;

class RandomExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('shuffleMe', array($this, 'shuffleFilter'))
        );
    }

    public function shuffleFilter(array $array)
    {
        shuffle($array);
        return $array;
    }

    public function getName()
    {
        return 'random_extension';
    }
}