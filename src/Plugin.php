<?php

namespace Mediact\TestingSuite\Composer;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Mediact\Composer\FileInstaller;
use Mediact\FileMapping\UnixFileMappingReader;
use Mediact\TestingSuite\Composer\Installer\ArchiveExcludeInstaller;
use Mediact\TestingSuite\Composer\Installer\FilesInstaller;
use Mediact\TestingSuite\Composer\Installer\GrumPhpInstaller;
use Mediact\TestingSuite\Composer\Installer\InstallerInterface;
use Mediact\TestingSuite\Composer\Installer\PackagesInstaller;
use Mediact\TestingSuite\Composer\Installer\PipelinesInstaller;

class Plugin implements PluginInterface, EventSubscriberInterface
{
    /**
     * @var InstallerInterface[]
     */
    private $installers;

    /**
     * Constructor.
     *
     * @param InstallerInterface[] ...$installers
     */
    public function __construct(InstallerInterface ...$installers)
    {
        $this->installers = $installers;
    }

    /**
     * Apply plugin modifications to Composer.
     *
     * @param Composer    $composer
     * @param IOInterface $io
     *
     * @return void
     */
    public function activate(Composer $composer, IOInterface $io)
    {
        $typeResolver    = new ProjectTypeResolver($composer);
        $mappingResolver = new MappingResolver($typeResolver);
        $fileInstaller   = new FileInstaller(
            new UnixFileMappingReader('', '')
        );

        $this->installers[] = new FilesInstaller($mappingResolver, $fileInstaller, $io);
        $this->installers[] = new GrumPhpInstaller($io);
        $this->installers[] = new ArchiveExcludeInstaller($mappingResolver, $io);
        $this->installers[] = new PackagesInstaller($composer, $typeResolver, $io);
        $this->installers[] = new PipelinesInstaller($fileInstaller, $io);
    }

    /**
     * Run the installers.
     *
     * @return void
     */
    public function install()
    {
        foreach ($this->installers as $installer) {
            $installer->install();
        }
    }

    /**
     * Subscribe to post update and post install command.
     *
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'post-install-cmd' => [
                'install'
            ],
            'post-update-cmd' => [
                'install'
            ]
        ];
    }
}
