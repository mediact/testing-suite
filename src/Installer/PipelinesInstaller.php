<?php
/**
 * Copyright MediaCT. All rights reserved.
 * https://www.mediact.nl
 */

namespace Mediact\TestingSuite\Composer\Installer;

use Composer\IO\IOInterface;
use Mediact\Composer\FileInstaller;
use Mediact\FileMapping\UnixFileMapping;
use Symfony\Component\Process\ProcessBuilder;

class PipelinesInstaller implements InstallerInterface
{
    /** @var FileInstaller */
    private $fileInstaller;

    /** @var IOInterface */
    private $io;

    /** @var string */
    private $pattern = 'bitbucket.org';

    /** @var string */
    private $filename = 'bitbucket-pipelines.yml';

    /** @var array */
    private $types = [
        'mediact' => 'MediaCT pipelines script',
        'basic'   => 'Basic pipelines script'
    ];
    /**
     * @var null|string
     */
    private $destination;
    /**
     * @var ProcessBuilder
     */
    private $processBuilder;

    /**
     * Constructor.
     *
     * @param FileInstaller  $fileInstaller
     * @param IOInterface    $io
     * @param ProcessBuilder $processBuilder
     * @param string|null    $destination
     * @param string|null    $pattern
     * @param string|null    $filename
     * @param array|null     $types
     */
    public function __construct(
        FileInstaller $fileInstaller,
        IOInterface $io,
        ProcessBuilder $processBuilder = null,
        string $destination = null,
        string $pattern = null,
        string $filename = null,
        array $types = null
    ) {
        $this->fileInstaller  = $fileInstaller;
        $this->io             = $io;
        $this->processBuilder = $processBuilder ?: new ProcessBuilder();
        $this->destination    = $destination ?: getcwd();

        $pattern  !== null && $this->pattern  = $pattern;
        $filename !== null && $this->filename = $filename;
        $types    !== null && $this->types    = $types;
    }

    /**
     * Install.
     *
     * @return void
     */
    public function install()
    {
        if (file_exists($this->destination . '/' . $this->filename)
            || !$this->isBitbucket()
        ) {
            return;
        }


        $mapping = new UnixFileMapping(
            sprintf(
                __DIR__ . '/../../templates/files/pipelines-%s',
                $this->chooseMapping()
            ),
            $this->destination,
            $this->filename
        );

        $this->fileInstaller->installFile($mapping);
        $this->io->write(
            sprintf(
                '<info>Installed:</info> %s',
                $mapping->getRelativeDestination()
            )
        );
    }

    /**
     * Choose the mapping to install.
     *
     * @return mixed
     */
    private function chooseMapping()
    {
        $labels = array_values($this->types);
        $keys   = array_keys($this->types);

        $selected = $this->io->select(
            'Bitbucket has been detected. Which pipelines script do you want to install?',
            $labels,
            0
        );

        return $keys[$selected];
    }

    /**
     * Check whether the project is on Bitbucket.
     *
     * @return bool
     */
    private function isBitbucket(): bool
    {
        $process = $this->processBuilder
            ->setPrefix('git remote -v')
            ->getProcess();

        $process->run();
        return strpos($process->getOutput(), $this->pattern) !== false;
    }
}
