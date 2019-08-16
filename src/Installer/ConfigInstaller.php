<?php
/**
 * Copyright MediaCT. All rights reserved.
 * https://www.mediact.nl
 */

namespace Mediact\TestingSuite\Composer\Installer;

use Composer\Factory;
use Composer\IO\IOInterface;
use Composer\Json\JsonFile;
use Mediact\TestingSuite\Composer\ConfigResolver;

/**
 * @SuppressWarnings(PHPMD.ShortVariable)
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class ConfigInstaller implements InstallerInterface
{
    /** @var JsonFile */
    private $file;

    /** @var ConfigResolver */
    private $resolver;

    /** @var IOInterface */
    private $io;

    /**
     * Constructor.
     *
     * @param ConfigResolver $resolver
     * @param IOInterface    $io
     * @param JsonFile|null  $file
     */
    public function __construct(
        ConfigResolver $resolver,
        IOInterface $io,
        JsonFile $file = null
    ) {
        $this->resolver = $resolver;
        $this->io       = $io;
        $this->file     = $file ?? new JsonFile(Factory::getComposerFile());
    }

    /**
     * Install.
     *
     * @return void
     */
    public function install()
    {
        $definition = $this->file->read();
        $config     = $definition['config'] ?? [];

        $config = array_replace_recursive(
            $this->resolver->resolve(),
            $config
        );

        $definition['config'] = $config;
        $this->file->write($definition);
    }
}
