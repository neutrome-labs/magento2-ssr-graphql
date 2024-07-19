<?php

namespace NeutromeLabs\SsrGraphql\Model\Framework;

use Magento\Framework\App\EnvironmentFactory;
use Magento\Framework\App\ObjectManager\Environment\Developer;

class SsrGraphqlEnvironmentFactory extends EnvironmentFactory
{

    public function createEnvironment()
    {
        return new Developer($this);
    }
}
