<?php

namespace CoderDojo\WebsiteBundle\Command;

class RemoveEventCommand
{
    /**
    * @var string
    */
    var $id;

    /**
    * RemoveEventCommand constructor
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