<?php
/**
 * Copyright MediaCT. All rights reserved.
 * https://www.mediact.nl
 */

namespace Mediact\TestingSuite\Composer\Installer;

use Composer\Factory;
use Composer\IO\IOInterface;
use Composer\Json\JsonFile;

class GrumPhpInstaller implements InstallerInterface
{
    /** @var JsonFile */
    private $file;

    /** @var IOInterface */
    private $io;

    /** @var string */
    private $destination;

    /**
     * Constructor.
     *
     * @param IOInterface|null $io
     * @param JsonFile         $file
     * @param string|null      $destination
     */
    public function __construct(
        IOInterface $io,
        JsonFile $file = null,
        string $destination = null
    ) {
        $this->file        = $file;
        $this->io          = $io;
        $this->file        = $file ?: new JsonFile(Factory::getComposerFile());
        $this->destination = $destination ?: getcwd();
    }

    /**
     * Install.
     *
     * @return void
     */
    public function install()
    {
        $grumPhpFile = $this->destination . '/grumphp.yml';

        if (file_exists($grumPhpFile)) {
            unlink($grumPhpFile);
            $this->io->write(
                sprintf(
                    '<comment>Removed:</comment> existing GrumPHP config file %s',
                    $grumPhpFile
                )
            );
        }

        $definition = $this->file->read();
        if (!empty($definition['extra']['grumphp']['config-default-path'])) {
            return;
        }

        if (!array_key_exists('extra', $definition)) {
            $definition['extra'] = [];
        }

        if (!array_key_exists('grumphp', $definition['extra'])) {
            $definition['extra']['grumphp'] = [];
        }

        $definition['extra']['grumphp']['config-default-path'] =
            'vendor/mediact/testing-suite/config/default/grumphp.yml';

        $this->file->write($definition);
        $this->io->write(
            '<info>Added:</info> GrumPHP config to composer.json'
        );
    }
}
