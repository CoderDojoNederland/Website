<?php

namespace CoderDojo\WebsiteBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class CoderDojoWebsiteBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
