<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2014 Zend Technologies USA Inc. (http://www.zend.com)
 */

namespace ZF\Hal\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use ZF\Hal\RendererOptions;

class RendererOptionsFactory implements FactoryInterface
{
    /**
     * {@inheritdoc}
     * @return RendererOptions
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('ZF\Hal\HalConfig');

        $rendererConfig = [];
        if (isset($config['renderer']) && is_array($config['renderer'])) {
            $rendererConfig = $config['renderer'];
        }

        if (isset($rendererConfig['render_embedded_resources'])
            && !isset($rendererConfig['render_embedded_entities'])
        ) {
            $rendererConfig['render_embedded_entities'] = $rendererConfig['render_embedded_resources'];
        }

        return new RendererOptions($rendererConfig);
    }
}
