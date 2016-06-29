<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2014 Zend Technologies USA Inc. (http://www.zend.com)
 */

namespace ZF\Hal\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use ZF\Hal\View\HalJsonRenderer;

class HalJsonRendererFactory implements FactoryInterface
{
    /**
     * {@inheritdoc}
     * @return HalJsonRenderer
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $helpers            = $container->get('ViewHelperManager');
        $apiProblemRenderer = $container->get('ZF\ApiProblem\ApiProblemRenderer');

        $renderer = new HalJsonRenderer($apiProblemRenderer);
        $renderer->setHelperPluginManager($helpers);

        return $renderer;
    }
}
