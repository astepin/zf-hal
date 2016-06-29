<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2014 Zend Technologies USA Inc. (http://www.zend.com)
 */

namespace ZFTest\Hal\Factory;

use PHPUnit_Framework_TestCase as TestCase;
use ReflectionObject;
use Zend\ServiceManager\ServiceManager;
use Zend\Hydrator\HydratorPluginManager;
use Zend\View\Helper\ServerUrl;
use Zend\View\Helper\Url;
use ZF\Hal\Factory\HalViewHelperFactory;
use ZF\Hal\RendererOptions;

class HalViewHelperFactoryTest extends TestCase
{
    /**
     * @var \Zend\ServiceManager\AbstractPluginManager
     */
    private $pluginManager;

    public function setupPluginManager($config = [])
    {
        if (isset($config['renderer']) && is_array($config['renderer'])) {
            $rendererOptions = new RendererOptions($config['renderer']);
        } else {
            $rendererOptions = new RendererOptions();
        }

        $metadataMap = $this->createMock('ZF\Hal\Metadata\MetadataMap');
        $metadataMap
            ->expects($this->once())
            ->method('getHydratorManager')
            ->will($this->returnValue(new HydratorPluginManager(new ServiceManager())));

        $this->pluginManager = $this->getMockBuilder('Zend\ServiceManager\AbstractPluginManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->pluginManager
            ->expects($this->at(0))
            ->method('get')
            ->with('ZF\Hal\HalConfig')
            ->will($this->returnValue($config));
        $this->pluginManager
            ->expects($this->at(1))
            ->method('get')
            ->with('ZF\Hal\RendererOptions')
            ->will($this->returnValue($rendererOptions));
        $this->pluginManager
            ->expects($this->at(2))
            ->method('get')
            ->with('ZF\Hal\MetadataMap')
            ->will($this->returnValue($metadataMap));

        $this->pluginManager
            ->expects($this->at(3))
            ->method('get')
            ->with('ServerUrl')
            ->will($this->returnValue(new ServerUrl()));

        $this->pluginManager
            ->expects($this->at(4))
            ->method('get')
            ->with('Url')
            ->will($this->returnValue(new Url()));
    }

    public function testInstantiatesHalViewHelper()
    {
        $this->setupPluginManager();

        $factory = new HalViewHelperFactory();
        $plugin = $factory($this->pluginManager, '');

        $this->assertInstanceOf('ZF\Hal\Plugin\Hal', $plugin);
    }

    /**
     * @group fail
     */
    public function testOptionUseProxyIfPresentInConfig()
    {
        $options = [
            'options' => [
                'use_proxy' => true,
            ],
        ];

        $this->setupPluginManager($options);

        $factory = new HalViewHelperFactory();
        $halPlugin = $factory($this->pluginManager, '');

        $r = new ReflectionObject($halPlugin);
        $p = $r->getProperty('serverUrlHelper');
        $p->setAccessible(true);
        $serverUrlPlugin = $p->getValue($halPlugin);
        $this->assertInstanceOf('Zend\View\Helper\ServerUrl', $serverUrlPlugin);

        $r = new ReflectionObject($serverUrlPlugin);
        $p = $r->getProperty('useProxy');
        $p->setAccessible(true);
        $useProxy = $p->getValue($serverUrlPlugin);
        $this->assertInternalType('boolean', $useProxy);
        $this->assertTrue($useProxy);
    }
}
