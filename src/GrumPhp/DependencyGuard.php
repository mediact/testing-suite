<?php
/**
 * Copyright MediaCT. All rights reserved.
 * https://www.mediact.nl
 */

namespace Mediact\TestingSuite\Composer\GrumPhp;

use Composer\Composer;
use GrumPHP\Task\TaskInterface;
use Mediact\DependencyGuard\Composer\Command\Exporter\ViolationExporterFactory;
use Mediact\DependencyGuard\DependencyGuardFactory;
use Mediact\DependencyGuard\GrumPHP\DependencyGuard as InnerDependencyGuard;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DependencyGuard implements TaskInterface
{
    use OptionalTaskTrait;

    /** @var Composer */
    private $composer;

    /** @var InputInterface */
    private $input;

    /** @var OutputInterface */
    private $output;

    /**
     * Constructor.
     *
     * @param Composer        $composer
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    public function __construct(
        Composer $composer,
        InputInterface $input,
        OutputInterface $output
    ) {
        $this->composer = $composer;
        $this->input    = $input;
        $this->output   = $output;
    }

    /**
     * @return TaskInterface
     */
    protected function createInnerTask(): TaskInterface
    {
        $exporterFactory = new ViolationExporterFactory();

        return new InnerDependencyGuard(
            $this->composer,
            new DependencyGuardFactory(),
            $exporterFactory->create(
                $this->input,
                $this->output
            )
        );
    }

    /**
     * Checks whether the current environment supports dependency guard.
     *
     * @return bool
     */
    protected function supportsInnerTask(): bool
    {
        return class_exists(InnerDependencyGuard::class, true);
    }
}
