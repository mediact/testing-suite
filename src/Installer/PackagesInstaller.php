<?php
/**
 * Copyright MediaCT. All rights reserved.
 * https://www.mediact.nl
 */

namespace Mediact\TestingSuite\Composer\Installer;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Package\Link;
use Composer\Semver\Comparator;
use Mediact\Composer\DependencyInstaller\DependencyInstaller;
use Mediact\TestingSuite\Composer\ProjectTypeResolver;

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
        'default' => [
            [
                'name' => 'mediact/dependency-guard',
                'version' => '@stable',
                'php' => '7.2.0'
            ]
        ],
        'magento1' => [
            [
                'name' => 'mediact/coding-standard-magento1',
                'version' => '@stable'
            ],
            [
                'name' => 'mediact/dependency-guard',
                'version' => '@stable',
                'php' => '7.2.0'
            ]
        ],
        'magento2' => [
            [
                'name' => 'mediact/coding-standard-magento2',
                'version' => '@stable'
            ],
            [
                'name' => 'mediact/dependency-guard',
                'version' => '@stable',
                'php' => '7.2.0'
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

        $rootPhpVersion = array_reduce(
            $this->composer->getPackage()->getRequires(),
            function (string $carry, Link $link): string {
                return $link->getTarget() === 'php'
                    ? $link->getPrettyConstraint()
                    : $carry;
            },
            PHP_VERSION
        );

        foreach ($this->mapping[$type] as $package) {
            $requiredPhpVersion = $package['php'] ?? PHP_VERSION;

            // Skip the package if the required PHP version is higher than the
            // current PHP version as defined by the root package.
            if (Comparator::compare($requiredPhpVersion, '>', $rootPhpVersion)) {
                continue;
            }

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
