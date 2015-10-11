<?php

namespace Coderdojo\WebsiteBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class CoderdojoWebsiteBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
