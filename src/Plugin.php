<?php

namespace MediaCT\TestingSuite\Composer;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\Script\Event;

class Plugin implements PluginInterface, EventSubscriberInterface
{
    /**
     * @var Composer
     */
    private $composer;

    /**
     * @var IOInterface
     */
    private $io;

    /**
     * @var Installer
     */
    private $installer;

    /**
     * Apply plugin modifications to Composer
     *
     * @param Composer    $composer
     * @param IOInterface $io
     *
     * @return void
     */
    public function activate(Composer $composer, IOInterface $io)
    {
        $this->composer = $composer;
        $this->io       = $io;

        $filePaths = $this->getFilePaths();

        $this->installer = new Installer(
            new UnixFileMappingReader(
                $filePaths,
                __DIR__ . '/../templates/files',
                getcwd()
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
     *
     * @return array
     */
    private function getFilePaths(): array
    {
        return [__DIR__ . '/../templates/mapping/files', $this->getPhpCsMappingPath()];
    }

    /**
     *
     * @return string
     */
    private function getPhpCsMappingPath(): string
    {
        $packageType = $this->composer->getPackage()->getType();

        if ($packageType === 'magento2-module') {
            return __DIR__ . '/../templates/mapping/phpcs/magento2';
        }

        if ($packageType === 'magento-module') {
            return __DIR__ . '/../templates/mapping/phpcs/magento1';
        }

        return __DIR__ . '/../templates/mapping/phpcs/default';
    }
}
