<?php

/**
 * Copyright MediaCT. All rights reserved.
 * https://www.mediact.nl
 */

namespace Mediact\TestingSuite\Composer\Tests\Installer;

use Composer\IO\IOInterface;
use Mediact\Composer\FileInstaller;
use Mediact\TestingSuite\Composer\Factory\ProcessFactoryInterface;
use Mediact\TestingSuite\Composer\ProjectTypeResolver;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\TestCase;
use Mediact\TestingSuite\Composer\Installer\PipelinesInstaller;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

/**
 * @coversDefaultClass \Mediact\TestingSuite\Composer\Installer\PipelinesInstaller
 * @SuppressWarnings(PHPMD)
 */
class PipelinesInstallerTest extends TestCase
{
    /**
     * @param array  $files
     * @param string $remotes
     * @param bool   $expectedInstall
     *
     * @return void
     * @dataProvider dataProvider
     *
     * @covers ::__construct
     * @covers ::install
     * @covers ::isBitbucket
     */
    public function testInstall(
        array $files,
        string $remotes,
        bool $expectedInstall
    ) {
        $fileInstaller  = $this->createMock(FileInstaller::class);
        $io             = $this->createMock(IOInterface::class);
        $processFactory = $this->createMock(ProcessFactoryInterface::class);
        $process        = $this->createMock(Process::class);
        $typeResolver   = $this->createMock(ProjectTypeResolver::class);
        $filesystem     = $this->createFilesystem($files);

        $process
            ->expects(self::any())
            ->method('run');

        $process
            ->expects(self::any())
            ->method('getOutput')
            ->willReturn($remotes);

        $processFactory
            ->expects(self::any())
            ->method('create')
            ->willReturn($process);

        if ($expectedInstall) {
            $fileInstaller
                ->expects(self::any())
                ->method('installFile');

            $typeResolver
                ->expects(self::once())
                ->method('resolve')
                ->willReturn('default');
        } else {
            $fileInstaller
                ->expects(self::never())
                ->method('installFile');

            $typeResolver
                ->expects(self::never())
                ->method('resolve');
        }

        $installer = new PipelinesInstaller(
            $fileInstaller,
            $io,
            $processFactory,
            $typeResolver,
            $filesystem->url()
        );

        $installer->install();
    }

    /**
     * @return array
     */
    public function dataProvider(): array
    {
        return [
            [
                [
                    'bitbucket-pipelines.yml'
                ],
                'origin bitbucket.org',
                false
            ],
            [
                [],
                'origin github.org',
                false
            ],
            [
                [],
                'origin bitbucket.org',
                true
            ],
            [
                [],
                'origin bitbucket.org',
                true
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
