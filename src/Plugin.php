<?php

namespace Mediact\TestingSuite\Composer;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\Script\Event;
use Mediact\Composer\FileInstaller;
use Mediact\FileMapping\UnixFileMappingReader;

class Plugin implements PluginInterface, EventSubscriberInterface
{
    /** @var Composer */
    private $composer;

    /** @var FileInstaller */
    private $installer;

    /** @var string[] */
    private $codingStandardsMapping = [
        'magento2-module' => 'magento2',
        'magento-module' => 'magento1'
    ];

    /**
     * Apply plugin modifications to Composer.
     *
     * @param Composer    $composer
     * @param IOInterface $io
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function activate(Composer $composer, IOInterface $io)
    {
        $this->composer = $composer;

        $this->installer = new FileInstaller(
            new UnixFileMappingReader(
                __DIR__ . '/../templates/files',
                getcwd(),
                ...$this->getFilePaths()
            )
        );
    }

    /**
     * Install the default configuration files.
     *
     * @param Event $event
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function installFiles(Event $event)
    {
        $this->installer->install($event->getIO());
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     * * The method name to call (priority defaults to 0).
     * * An array composed of the method name to call and the priority.
     * * An array of arrays composed of the method names to call and respective
     *   priorities, or 0 if unset.
     *
     * For instance:
     *
     * * ['eventName' => 'methodName']
     * * ['eventName' => ['methodName', $priority]]
     * * ['eventName' => [['methodName1', $priority], ['methodName2']]
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'post-install-cmd' => 'installFiles',
            'post-update-cmd' => 'installFiles'
        ];
    }

    /**
     * @return string[]
     */
    private function getFilePaths(): array
    {
        return [
            __DIR__ . '/../templates/mapping/files',
            $this->getPhpCsMappingPath()
        ];
    }

    /**
     * @return string
     */
    private function getPhpCsMappingPath(): string
    {
        $packageType = $this->composer->getPackage()->getType();

        $file = array_key_exists($packageType, $this->codingStandardsMapping)
            ? $this->codingStandardsMapping[$packageType]
            : 'default';

        return __DIR__ . '/../templates/mapping/phpcs/' . $file;
    }
}
