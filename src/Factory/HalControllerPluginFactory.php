<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2014 Zend Technologies USA Inc. (http://www.zend.com)
 */

namespace ZF\Hal\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class HalControllerPluginFactory implements FactoryInterface
{
    /**
     * {@inheritdoc}
     * @return \ZF\Hal\Plugin\Hal
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $helpers  = $container->get('ViewHelperManager');
        return $helpers->get('Hal');
    }
}
