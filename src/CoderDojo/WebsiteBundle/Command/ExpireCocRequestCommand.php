<?php

namespace CoderDojo\WebsiteBundle\Command;

class ExpireCocRequestCommand
{
    /**
    * @var string
    */
    private $id;

    /**
    * ExpireCocRequestCommand constructor
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
