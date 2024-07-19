<?php

namespace NeutromeLabs\SsrGraphql\Model\Framework;

use Magento\Framework\App\ObjectManagerFactory;

class SsrGraphqlObjectManagerFactory extends ObjectManagerFactory
{

    protected $envFactoryClassName = SsrGraphqlEnvironmentFactory::class;
}
