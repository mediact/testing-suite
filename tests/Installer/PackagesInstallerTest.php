<?php
/**
 * Copyright MediaCT. All rights reserved.
 * https://www.mediact.nl
 */

namespace Mediact\TestingSuite\Composer\Tests\Installer;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Package\Link;
use Composer\Package\Package;
use Mediact\Composer\DependencyInstaller\DependencyInstaller;
use Mediact\TestingSuite\Composer\ProjectTypeResolver;
use PHPUnit\Framework\TestCase;
use Mediact\TestingSuite\Composer\Installer\PackagesInstaller;

/**
 * @coversDefaultClass \Mediact\TestingSuite\Composer\Installer\PackagesInstaller
 * @SuppressWarnings(PHPMD)
 */
class PackagesInstallerTest extends TestCase
{
    /**
     * @param string     $type
     * @param array      $requires
     * @param array|null $expected
     *
     * @return void
     * @dataProvider dataProvider
     *
     * @covers ::__construct
     * @covers ::install
     * @covers ::isPackageRequired
     */
    public function testInstall(
        string $type,
        array $requires,
        array $expected = null
    ) {
        $composer     = $this->createMock(Composer::class);
        $package      = $this->createMock(Package::class);
        $typeResolver = $this->createMock(ProjectTypeResolver::class);
        $depInstaller = $this->createMock(DependencyInstaller::class);
        $io           = $this->createMock(IOInterface::class);

        $composer
            ->expects(self::any())
            ->method('getPackage')
            ->willReturn($package);

        $package
            ->expects(self::any())
            ->method('getRequires')
            ->willReturn($requires);

        $typeResolver
            ->expects(self::any())
            ->method('resolve')
            ->willReturn($type);

        $installer = new PackagesInstaller(
            $composer,
            $typeResolver,
            $io,
            $depInstaller
        );

        if ($expected) {
            $depInstaller
                ->expects(self::exactly(count($expected)))
                ->method('installPackage')
                ->withConsecutive(...$expected);
        } else {
            $depInstaller
                ->expects(self::never())
                ->method('installPackage');
        }

        $installer->install();
    }

    /**
     * @return array
     */
    public function dataProvider(): array
    {
        return [
            [
                'magento1',
                $this->createLinkMocks(['foo/bar']),
                [['mediact/coding-standard-magento1']]
            ],
            [
                'magento1',
                $this->createLinkMocks(
                    ['foo/bar', 'mediact/coding-standard-magento1']
                ),
                null
            ],
            [
                'magento2',
                $this->createLinkMocks(['foo/bar']),
                [['mediact/coding-standard-magento2']]
            ],
            [
                'default',
                $this->createLinkMocks(['foo/bar']),
                null
            ],
            [
                'unknown',
                $this->createLinkMocks(['foo/bar']),
                null
            ]
        ];
    }

    /**
     * @param string[] $targets
     *
     * @return Link[]
     */
    private function createLinkMocks(array $targets): array
    {
        return array_map(
            function (string $target): Link {
                /** @var Link $mock */
                $mock = $this->createConfiguredMock(
                    Link::class,
                    ['getTarget' => $target]
                );

                return $mock;
            },
            $targets
        );
    }
}
