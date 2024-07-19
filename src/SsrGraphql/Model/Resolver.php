<?php

namespace NeutromeLabs\SsrGraphql\Model;

use Magento\Framework\App\Area;
use Magento\Framework\App\ObjectManager\ConfigLoader;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\QueryProcessor;
use Magento\Framework\GraphQl\Schema;
use Magento\Framework\GraphQl\Schema\SchemaGeneratorInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\GraphQl\Model\Query\ContextFactory;
use NeutromeLabs\SsrGraphql\Api\ResolverInterface;
use NeutromeLabs\SsrGraphql\Model\Framework\SsrGraphqlObjectManagerFactory;
use NeutromeLabs\SsrGraphql\Service\ConfigManager;

class Resolver implements ResolverInterface
{

    private ?ObjectManagerInterface $graphQlObjectManager = null;

    private ?Schema $schema = null;


    public function __construct(
        private readonly SsrGraphqlObjectManagerFactory $objectManagerFactory,
        private readonly ConfigLoader                   $objectManagerConfigLoader,
        private readonly ConfigManager                  $configManager
    )
    {
    }


    private function getGraphqlObjectManager(): ObjectManagerInterface
    {
        if (!$this->graphQlObjectManager) {
            $this->graphQlObjectManager = $this->objectManagerFactory->create([]);
            $this->graphQlObjectManager->configure($this->objectManagerConfigLoader->load(Area::AREA_GRAPHQL));
            $this->graphQlObjectManager->get(\Magento\Framework\App\State::class)->setAreaCode(Area::AREA_GRAPHQL);
        }

        return $this->graphQlObjectManager;
    }

    private function getQueryProcessor(): QueryProcessor
    {
        return $this->getGraphqlObjectManager()->get(QueryProcessor::class);
    }

    private function getSchemaGenerator(): SchemaGeneratorInterface
    {
        return $this->getGraphqlObjectManager()->get(SchemaGeneratorInterface::class);
    }

    private function getSchema(): Schema
    {
        if (!$this->schema) {
            $this->schema = $this->getSchemaGenerator()->generate();
        }
        return $this->schema;
    }

    private function getContextFactory(): ContextFactory
    {
        return $this->getGraphqlObjectManager()->get(ContextFactory::class);
    }

    /**
     * @throws GraphQlInputException
     * @throws LocalizedException
     */
    public function resolve(string $query, array $variables = []): array
    {
        $microtime = microtime(true);

        $data = $this->getQueryProcessor()->process(
            $this->getSchema(),
            $query,
            $this->getContextFactory()->create(),
            $variables
        );

        if ($this->configManager->getDebug()) {
            $duration = microtime(true) - $microtime;
            $data['__debug'] = [
                'resolve_total_sec' => $duration,
            ];
        }

        return $data;
    }
}
