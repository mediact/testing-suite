<?php
/**
 * Copyright Mediact. All rights reserved.
 * https://www.Mediact.nl
 */

namespace Mediact\TestingSuite\Composer\Tests;

use Composer\Composer;
use Composer\IO\IOInterface;
use Mediact\TestingSuite\Composer\Installer\InstallerInterface;
use Mediact\TestingSuite\Composer\Plugin;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

/**
 * @coversDefaultClass \Mediact\TestingSuite\Composer\Plugin
 */
class PluginTest extends TestCase
{
    /**
     * @return void
     *
     * @covers ::activate
     */
    public function testActivate()
    {
        $plugin = new Plugin();
        $plugin->activate(
            $this->createMock(Composer::class),
            $this->createMock(IOInterface::class)
        );

        $reflection = new ReflectionProperty(Plugin::class, 'installers');
        $reflection->setAccessible(true);

        $this->assertContainsOnlyInstancesOf(
            InstallerInterface::class,
            $reflection->getValue($plugin)
        );
    }

    /**
     * @return void
     *
     * @covers ::__construct
     * @covers ::install
     */
    public function testInstall()
    {
        $installers = [
            $this->createMock(InstallerInterface::class),
            $this->createMock(InstallerInterface::class)
        ];

        foreach ($installers as $installer) {
            $installer
                ->expects(self::once())
                ->method('install');
        }

        $plugin = new Plugin(...$installers);
        $plugin->install();
    }

    /**
     * @return void
     *
     * @covers ::getSubscribedEvents
     */
    public function testGetSubscribesEvents()
    {
        $plugin = new Plugin();

        foreach (Plugin::getSubscribedEvents() as $event => $methods) {
            foreach ($methods as $method) {
                $this->assertTrue(method_exists($plugin, $method));
            }
        }
    }
}
