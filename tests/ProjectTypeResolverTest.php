<?php
/**
 * Copyright MediaCT. All rights reserved.
 * https://www.mediact.nl
 */

namespace Mediact\TestingSuite\Composer\Tests;

use Composer\Package\RootPackageInterface;
use Composer\Composer;
use Mediact\TestingSuite\Composer\ProjectTypeResolver;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Mediact\TestingSuite\Composer\ProjectTypeResolver
 */
class ProjectTypeResolverTest extends TestCase
{
    /**
     * @param string $packageType
     * @param string $expected
     *
     * @return void
     *
     * @dataProvider dataProvider
     *
     * @covers ::__construct
     * @covers ::resolve
     */
    public function testToString(string $packageType, string $expected)
    {
        $composer = $this->createMock(Composer::class);
        $package  = $this->createMock(RootPackageInterface::class);

        $composer
            ->expects(self::once())
            ->method('getPackage')
            ->willReturn($package);

        $package
            ->expects(self::once())
            ->method('getType')
            ->willReturn($packageType);

        $decider = new ProjectTypeResolver($composer);
        $this->assertEquals($expected, $decider->resolve());
    }

    /**
     * @return array
     */
    public function dataProvider(): array
    {
        return [
            ['some-type', 'default'],
            ['magento-module', 'magento1'],
            ['magento2-module', 'magento2']
        ];
    }
}
