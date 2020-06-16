<?php

/**
 * Copyright MediaCT. All rights reserved.
 * https://www.mediact.nl
 */

namespace Mediact\TestingSuite\Composer\Installer;

use Composer\IO\IOInterface;
use Mediact\Composer\FileInstaller as ComposerFileInstaller;
use Mediact\TestingSuite\Composer\MappingResolver;

/**
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class FilesInstaller implements InstallerInterface
{
    /** @var MappingResolver */
    private $mappingResolver;

    /** @var ComposerFileInstaller */
    private $fileInstaller;

    /** @var IOInterface */
    private $io;

    /**
     * Constructor.
     *
     * @param MappingResolver       $mappingResolver
     * @param ComposerFileInstaller $fileInstaller
     * @param IOInterface           $io
     */
    public function __construct(
        MappingResolver $mappingResolver,
        ComposerFileInstaller $fileInstaller,
        IOInterface $io
    ) {
        $this->mappingResolver = $mappingResolver;
        $this->fileInstaller   = $fileInstaller;
        $this->io              = $io;
    }

    /**
     * Install.
     *
     * @return void
     */
    public function install()
    {
        foreach ($this->mappingResolver->resolve() as $mapping) {
            if (file_exists($mapping->getDestination())) {
                continue;
            }

            $this->fileInstaller->installFile($mapping);

            $this->io->write(
                sprintf(
                    '<info>Installed:</info> %s',
                    $mapping->getRelativeDestination()
                )
            );
        }
    }
}
