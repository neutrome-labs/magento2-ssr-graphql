<?php

namespace NeutromeLabs\SsrGraphql\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\StoreManagerInterface;
use NeutromeLabs\SsrGraphql\Api\ResolverInterface;
use NeutromeLabs\SsrGraphql\Service\ConfigManager;

class SsrGraphqlViewModel implements ArgumentInterface
{

    public function __construct(
        private readonly ConfigManager         $configManager,
        private readonly StoreManagerInterface $storeManager,
        private readonly ResolverInterface     $resolver,
    )
    {
    }

    public function isDebug(): bool
    {
        return $this->configManager->getDebug();
    }

    public function getBaseUrl(): string
    {
        return $this->storeManager->getStore()->getBaseUrl();
    }

    public function makeSsrGqlCall(string $query, array $variables = [], array $data = null): string
    {
        if (!is_array($data)) {
            $data = $this->resolver->resolve($query, $variables);
        }

        $query = json_encode($query);
        $variables = json_encode($variables);
        $data = json_encode($data);

        return <<<JS
window.createMagento2SsrGqlStub($query, $variables, $data)
JS;
    }
}
