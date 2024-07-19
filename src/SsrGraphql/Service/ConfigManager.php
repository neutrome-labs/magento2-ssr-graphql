<?php

namespace NeutromeLabs\SsrGraphql\Service;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class ConfigManager
{

    public function __construct(
        private readonly StoreManagerInterface $storeManager,
        private readonly ScopeConfigInterface  $scopeConfig
    )
    {
    }

    private function get(string $path): mixed
    {
        return $this->scopeConfig->getValue(
            'neutromelabs_ssrgraphql/' . $path,
            $this->storeManager->getStore() ? ScopeInterface::SCOPE_STORE : ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            $this->storeManager->getStore() ? $this->storeManager->getStore()->getId() : null,
        );
    }

    public function getDebug(): bool
    {
        return (bool)$this->get('general/debug');
    }
}
