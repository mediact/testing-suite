<?php
/**
 * Copyright MediaCT. All rights reserved.
 * https://www.mediact.nl
 */

namespace Mediact\TestingSuite\Composer\Installer;

use Composer\Factory;
use Composer\IO\IOInterface;
use Composer\Json\JsonFile;
use Mediact\FileMapping\FileMappingInterface;
use Mediact\TestingSuite\Composer\MappingResolver;

class ArchiveExcludeInstaller implements InstallerInterface
{
    /** @var JsonFile */
    private $file;

    /** @var MappingResolver */
    private $resolver;

    /** @var IOInterface */
    private $io;

    /** @var string */
    private $destination;

    /** @var array */
    private $defaults = [
        '/bitbucket-pipelines.yml',
        '/.gitignore',
        '/tests'
    ];

    /**
     * Constructor.
     *
     * @param MappingResolver $resolver
     * @param IOInterface     $io
     * @param JsonFile|null   $file
     * @param string          $destination
     * @param array|null      $defaults
     */
    public function __construct(
        MappingResolver $resolver,
        IOInterface $io,
        JsonFile $file = null,
        string $destination = null,
        array $defaults = null
    ) {
        $this->resolver    = $resolver;
        $this->io          = $io;
        $this->file        = $file ?: new JsonFile(Factory::getComposerFile());
        $this->destination = $destination ?: getcwd();

        $defaults !== null && $this->defaults = $defaults;
    }

    /**
     * Install.
     *
     * @return void
     */
    public function install()
    {
        $definition = $this->file->read();
        $excluded   = isset($definition['archive']['exclude'])
            ? $definition['archive']['exclude']
            : [];

        $excluded = array_map(
            function (string $exclude): string {
                return substr($exclude, 0, 1) !== '/'
                    ? '/' . $exclude
                    : $exclude;
            },
            $excluded
        );

        $files = array_merge(
            $this->defaults,
            array_map(
                function (FileMappingInterface $mapping): string {
                    return '/' . $mapping->getRelativeDestination();
                },
                iterator_to_array(
                    $this->resolver->resolve()
                )
            )
        );

        foreach ($files as $file) {
            if (!in_array($file, $excluded)
                && file_exists($this->destination . $file)
            ) {
                $excluded[] = $file;
                $this->io->write(
                    sprintf(
                        '<info>Added:</info> %s to archive exclude in composer.json',
                        $file
                    )
                );
            }
        }

        $definition['archive']['exclude'] = $excluded;
        $this->file->write($definition);
    }
}
