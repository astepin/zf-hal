<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2014 Zend Technologies USA Inc. (http://www.zend.com)
 */

namespace ZF\Hal\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use ZF\Hal\Exception;
use ZF\Hal\Extractor\LinkCollectionExtractor;
use ZF\Hal\Extractor\LinkExtractor;
use ZF\Hal\Plugin;

class HalViewHelperFactory implements FactoryInterface
{
    /**
     * {@inheritdoc}
     * @return Plugin\Hal
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $halConfig       = $container->get('ZF\Hal\HalConfig');
        /* @var $rendererOptions \ZF\Hal\RendererOptions */
        $rendererOptions = $container->get('ZF\Hal\RendererOptions');
        /** @var \ZF\Hal\Metadata\MetadataMap $metadataMap */
        $metadataMap     = $container->get('ZF\Hal\MetadataMap');
        $hydrators       = $metadataMap->getHydratorManager();

        $serverUrlHelper = $container->get('ServerUrl');
        if (isset($halConfig['options']['use_proxy'])) {
            $serverUrlHelper->setUseProxy($halConfig['options']['use_proxy']);
        }

        $urlHelper = $container->get('Url');

        $helper = new Plugin\Hal($hydrators);
        $helper
            ->setMetadataMap($metadataMap)
            ->setServerUrlHelper($serverUrlHelper)
            ->setUrlHelper($urlHelper);

        $linkExtractor = new LinkExtractor($serverUrlHelper, $urlHelper);
        $linkCollectionExtractor = new LinkCollectionExtractor($linkExtractor);
        $helper->setLinkCollectionExtractor($linkCollectionExtractor);

        $defaultHydrator = $rendererOptions->getDefaultHydrator();
        if ($defaultHydrator) {
            if (! $hydrators->has($defaultHydrator)) {
                throw new Exception\DomainException(sprintf(
                    'Cannot locate default hydrator by name "%s" via the HydratorManager',
                    $defaultHydrator
                ));
            }

            $hydrator = $hydrators->get($defaultHydrator);
            $helper->setDefaultHydrator($hydrator);
        }

        $helper->setRenderEmbeddedEntities($rendererOptions->getRenderEmbeddedEntities());
        $helper->setRenderCollections($rendererOptions->getRenderEmbeddedCollections());

        $hydratorMap = $rendererOptions->getHydrators();
        foreach ($hydratorMap as $class => $hydratorServiceName) {
            $helper->addHydrator($class, $hydratorServiceName);
        }

        return $helper;
    }
}
