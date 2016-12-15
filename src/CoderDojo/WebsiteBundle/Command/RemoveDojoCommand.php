<?php

namespace CoderDojo\WebsiteBundle\Command;

class RemoveDojoCommand
{
    /**
     * @var int
     **/
    private $id;

    /**
     * RemoveDojoCommand constructor.
     * @param $id
     */
    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
}