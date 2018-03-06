<?php
/**
 * Copyright MediaCT. All rights reserved.
 * https://www.mediact.nl
 */

namespace Mediact\TestingSuite\Composer\Tests\Installer;

use Composer\IO\IOInterface;
use Composer\Json\JsonFile;
use Mediact\TestingSuite\Composer\ConfigResolver;
use PHPUnit\Framework\TestCase;
use Mediact\TestingSuite\Composer\Installer\ConfigInstaller;

/**
 * @coversDefaultClass \Mediact\TestingSuite\Composer\Installer\ConfigInstaller
 */
class ConfigInstallerTest extends TestCase
{
    /**
     * @return void
     *
     * @covers ::__construct
     * @covers ::install
     */
    public function testInstall(): void
    {
        $resolver = $this->createMock(ConfigResolver::class);
        $io       = $this->createMock(IOInterface::class);
        $file     = $this->createMock(JsonFile::class);

        $installer = new ConfigInstaller($resolver, $io, $file);

        $resolverOutput = [
            'sort-packages' => true
        ];

        $configWrite = [
            'config' => $resolverOutput
        ];

        $file
            ->expects(self::once())
            ->method('read')
            ->willReturn([]);

        $resolver
            ->expects(self::once())
            ->method('resolve')
            ->willReturn($resolverOutput);

        $file
            ->expects(self::once())
            ->method('write')
            ->with($configWrite);

        $installer->install();
    }

    /**
     * @return array
     */
    public function dataProvider(): array
    {
        return [
            [
                [],
                [
                    'sort-packages' => true
                ]
            ],
            [
                [],
                [
                    'extra' => [
                        'grumphp' => [
                            'config-default-path' => 'vendor/mediact/testing-suite/config/default/grumphp.yml'
                        ]
                    ]
                ]
            ]
        ];
    }
}
