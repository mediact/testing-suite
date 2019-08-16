<?php
/**
 * Copyright MediaCT. All rights reserved.
 * https://www.mediact.nl
 */

namespace Mediact\TestingSuite\Composer\Installer;

use Composer\Composer;
use Composer\IO\IOInterface;
use Mediact\Composer\DependencyInstaller\DependencyInstaller;
use Mediact\TestingSuite\Composer\ProjectTypeResolver;

/**
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class PackagesInstaller implements InstallerInterface
{
    /** @var DependencyInstaller */
    private $installer;

    /** @var Composer */
    private $composer;

    /** @var ProjectTypeResolver */
    private $typeResolver;

    /** @var IOInterface */
    private $io;

    /** @var array */
    private $mapping = [
        'default' => [],
        'magento1' => [
            [
                'name' => 'mediact/coding-standard-magento1',
                'version' => '@stable'
            ]
        ],
        'magento2' => [
            [
                'name' => 'mediact/coding-standard-magento2',
                'version' => '@stable'
            ]
        ]
    ];

    /**
     * Constructor.
     *
     * @param Composer                 $composer
     * @param ProjectTypeResolver      $typeResolver
     * @param IOInterface              $io
     * @param DependencyInstaller|null $installer
     * @param array                    $mapping
     */
    public function __construct(
        Composer $composer,
        ProjectTypeResolver $typeResolver,
        IOInterface $io,
        DependencyInstaller $installer = null,
        array $mapping = null
    ) {
        $this->composer     = $composer;
        $this->typeResolver = $typeResolver;
        $this->io           = $io;
        $this->installer    = $installer ?? new DependencyInstaller();
        $this->mapping      = $mapping ?? $this->mapping;
    }

    /**
     * Install.
     *
     * @return void
     */
    public function install()
    {
        $type = $this->typeResolver->resolve();
        if (!isset($this->mapping[$type])) {
            return;
        }

        foreach ($this->mapping[$type] as $package) {
            if (!$this->isPackageRequired($package['name'])) {
                $this->io->write(
                    sprintf('Requiring package %s', $package['name'])
                );

                $this->installer->installPackage(
                    $package['name'],
                    $package['version']
                );
            }
        }
    }

    /**
     * Whether a package has been required.
     *
     * @param string $packageName
     *
     * @return bool
     */
    private function isPackageRequired(string $packageName): bool
    {
        foreach ($this->composer->getPackage()->getRequires() as $require) {
            if ($require->getTarget() === $packageName) {
                return true;
            }
        }

        return false;
    }
}
