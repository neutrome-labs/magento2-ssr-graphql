<?php

namespace NeutromeLabs\SsrGraphql\Controller\Example;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\View\Result\PageFactory;
use NeutromeLabs\SsrGraphql\Service\ConfigManager;

class Index implements HttpGetActionInterface
{

    public function __construct(
        private readonly ConfigManager $configManager,
        private readonly PageFactory $pageFactory
    )
    {
    }

    /** @inheirtDoc */
    public function execute()
    {
        if (!$this->configManager->getDebug()) {
            // return 404;
        }

        return $this->pageFactory->create();
    }
}
