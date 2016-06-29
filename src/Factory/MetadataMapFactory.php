<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2014 Zend Technologies USA Inc. (http://www.zend.com)
 */

namespace ZF\Hal\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\Hydrator\HydratorPluginManager;
use ZF\Hal\Metadata;

class MetadataMapFactory implements FactoryInterface
{
    /**
     * {@inheritdoc}
     * @return Metadata\MetadataMap
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('ZF\Hal\HalConfig');

        if ($container->has('HydratorManager')) {
            $hydrators = $container->get('HydratorManager');
        } else {
            $hydrators = new HydratorPluginManager($container);
        }

        $map = [];
        if (isset($config['metadata_map']) && is_array($config['metadata_map'])) {
            $map = $config['metadata_map'];
        }

        return new Metadata\MetadataMap($map, $hydrators);
    }
}
