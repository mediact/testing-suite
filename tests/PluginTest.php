<?php
/**
 * Created by PhpStorm.
 * User: johmanx
 * Date: 5/10/17
 * Time: 2:18 PM
 */

namespace Mediact\TestingSuite\Composer\Tests;

use Composer\Package\RootPackageInterface;
use Mediact\TestingSuite\Composer\Plugin;
use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Script\Event;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * @coversDefaultClass \Mediact\TestingSuite\Composer\Plugin
 */
class PluginTest extends TestCase
{
    /**
     * @param string $packageType
     *
     * @return Composer
     */
    private function createComposer(string $packageType = 'project'): Composer
    {
        /** @var Composer|PHPUnit_Framework_MockObject_MockObject $composer */
        $composer = $this->createMock(Composer::class);
        $package  = $this->createMock(RootPackageInterface::class);

        $composer
            ->expects($this->once())
            ->method('getPackage')
            ->willReturn($package);

        $package
            ->expects($this->once())
            ->method('getType')
            ->willReturn($packageType);

        return $composer;
    }

    /**
     * @return Plugin
     *
     * @covers ::activate
     * @covers ::getFilePaths
     * @covers ::getPhpCsMappingPath
     */
    public function testActivate(): Plugin
    {
        /** @var IOInterface $ioMock */
        $ioMock = $this->createMock(IOInterface::class);
        $plugin = new Plugin();

        $plugin->activate($this->createComposer(), $ioMock);
        $plugin->activate($this->createComposer('magento-module'), $ioMock);
        $plugin->activate($this->createComposer('magento2-module'), $ioMock);

        return $plugin;
    }
    
    /**
     * @depends testActivate
     *
     * @param Plugin $plugin
     *
     * @return void
     * @covers ::installFiles
     */
    public function testInstallFiles(Plugin $plugin)
    {
        /** @var Event|PHPUnit_Framework_MockObject_MockObject $event */
        $event = $this->createMock(Event::class);

        $event
            ->expects($this->once())
            ->method('getIO')
            ->willReturn($this->createMock(IOInterface::class));

        $plugin->installFiles($event);
    }

    /**
     * @depends testActivate
     *
     * @param Plugin $plugin
     *
     * @return void
     * @covers ::getSubscribedEvents
     */
    public function testGetSubscribesEvents(Plugin $plugin)
    {
        foreach (Plugin::getSubscribedEvents() as $event => $method) {
            $this->assertTrue(method_exists($plugin, $method));
            $this->assertInternalType('string', $event);
        }
    }
}
