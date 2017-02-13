<?php

namespace CoderDojo\WebsiteBundle\Command;

class ShipCocRequestCommand
{
    /**
    * @var string
    */
    private $id;

    /**
    * ShipCocRequestCommandCommand constructor
    *
    * @param string $id
    */
    public function __construct($id)
    {
        $this->id = $id;
    }
    
    /**
    * @return string
    */
    public function getId()
    {
        return $this->id;
    }
}