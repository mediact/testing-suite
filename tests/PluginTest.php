<?php
/**
 * Copyright Mediact. All rights reserved.
 * https://www.Mediact.nl
 */

namespace Mediact\TestingSuite\Composer\Tests;

use Composer\Package\RootPackageInterface;
use Mediact\Composer\DependencyInstaller\DependencyInstaller;
use Mediact\TestingSuite\Composer\Plugin;
use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Script\Event;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use ReflectionMethod;
use ReflectionProperty;

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
            ->expects($this->atLeastOnce())
            ->method('getPackage')
            ->willReturn($package);

        $package
            ->expects($this->atLeastOnce())
            ->method('getType')
            ->willReturn($packageType);

        return $composer;
    }

    /**
     * @return Plugin
     *
     * @covers ::activate
     * @covers ::getFilePaths
     * @covers ::getPhpCsMappingFile
     * @covers ::getType
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
            ->expects($this->atLeastOnce())
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
            if (is_array($method)) {
                foreach ($method as $nestedMethod) {
                    if (is_array($nestedMethod)) {
                        foreach ($nestedMethod as $deepestMethod) {
                            $this->assertTrue(
                                method_exists($plugin, $deepestMethod)
                            );
                            break;
                        }
                    } else {
                        $this->assertTrue(method_exists($plugin, $nestedMethod));
                        break;
                    }
                }
            } else {
                $this->assertTrue(method_exists($plugin, $method));
            }
            $this->assertInternalType('string', $event);
        }
    }

    /**
     * @depends testActivate
     *
     * @param Plugin $plugin
     *
     * @return void
     * @covers ::installRepositories
     * @covers ::getType
     */
    public function testInstallRepositories(Plugin $plugin)
    {
        $installer = $this->createMock(DependencyInstaller::class);

        $property = new ReflectionProperty($plugin, 'dependencyInstaller');
        $property->setAccessible(true);
        $property->setValue($plugin, $installer);
        $property->setAccessible(false);

        $method = new ReflectionMethod($plugin, 'getType');
        $method->setAccessible(true);
        $type = $method->invoke($plugin);
        $method->setAccessible(false);

        if ($type === 'default') {
            $installer
                ->expects($this->never())
                ->method('installRepository')
                ->with(
                    $this->isType('string'),
                    $this->isType('string'),
                    $this->isType('string')
                );
        } elseif (in_array($type, ['magento2', 'magento1'])) {
            $installer
                ->expects($this->atLeastOnce())
                ->method('installRepository')
                ->with(
                    $this->isType('string'),
                    $this->isType('string'),
                    $this->isType('string')
                );
        }

        $plugin->installRepositories();
    }

    /**
     * @depends testActivate
     *
     * @param Plugin $plugin
     *
     * @return void
     * @covers ::installPackages
     * @covers ::getType
     */
    public function testInstallPackages(Plugin $plugin)
    {
        $installer = $this->createMock(DependencyInstaller::class);

        $property = new ReflectionProperty($plugin, 'dependencyInstaller');
        $property->setAccessible(true);
        $property->setValue($plugin, $installer);
        $property->setAccessible(false);

        $method = new ReflectionMethod($plugin, 'getType');
        $method->setAccessible(true);
        $type = $method->invoke($plugin);
        $method->setAccessible(false);

        if ($type === 'default') {
            $installer
                ->expects($this->never())
                ->method('installPackage')
                ->with($this->isType('string'));
        } elseif (in_array($type, ['magento2', 'magento1'])) {
            $installer
                ->expects($this->atLeastOnce())
                ->method('installPackage')
                ->with($this->isType('string'));
        }

        $plugin->installPackages();
    }
}
