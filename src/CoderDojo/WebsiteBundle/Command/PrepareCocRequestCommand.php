<?php

namespace CoderDojo\WebsiteBundle\Command;

class PrepareCocRequestCommand
{
    /**
    * @var string
    */
    private $id;

    /**
    * PrepareCocRequestCommandCommand constructor
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