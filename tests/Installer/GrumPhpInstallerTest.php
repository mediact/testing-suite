<?php
/**
 * Copyright MediaCT. All rights reserved.
 * https://www.mediact.nl
 */

namespace Mediact\TestingSuite\Composer\Tests\Installer;

use Composer\IO\IOInterface;
use Composer\Json\JsonFile;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\TestCase;
use Mediact\TestingSuite\Composer\Installer\GrumPhpInstaller;

/**
 * @coversDefaultClass \Mediact\TestingSuite\Composer\Installer\GrumPhpInstaller
 */
class GrumPhpInstallerTest extends TestCase
{
    /**
     * @param array      $files
     * @param array      $definition
     * @param array|null $expectedWrite
     *
     * @return void
     * @dataProvider dataProvider
     *
     * @covers ::__construct
     * @covers ::install
     */
    public function testInstall(
        array $files,
        array $definition,
        array $expectedWrite = null
    ) {
        $io         = $this->createMock(IOInterface::class);
        $file       = $this->createMock(JsonFile::class);
        $filesystem = $this->createFilesystem($files);

        $file
            ->expects(self::once())
            ->method('read')
            ->willReturn($definition);

        if ($expectedWrite === null) {
            $file
                ->expects(self::never())
                ->method('write');
        } else {
            $file
                ->expects(self::once())
                ->method('write')
                ->with($expectedWrite);
        }

        $installer = new GrumPhpInstaller($io, $file, $filesystem->url());
        $installer->install();

        $this->assertFalse(file_exists($filesystem->url() . '/grumphp.yml'));
    }

    /**
     * @return array
     */
    public function dataProvider(): array
    {
        return [
            [
                [
                    'grumphp.yml'
                ],
                [
                    'extra' => [
                        'grumphp' => [
                            'config-default-path' => 'some-value'
                        ]
                    ]
                ],
                null
            ],
            [
                [],
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

    /**
     * @param array $files
     *
     * @return vfsStreamDirectory
     */
    private function createFilesystem(array $files): vfsStreamDirectory
    {
        return vfsStream::setup(
            sha1(__METHOD__ . mt_rand()),
            null,
            array_map('strval', array_flip($files))
        );
    }
}
