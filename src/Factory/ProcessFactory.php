<?php
/**
 * Copyright MediaCT. All rights reserved.
 * https://www.mediact.nl
 */

namespace Mediact\TestingSuite\Composer\Factory;

use Symfony\Component\Process\Process;

class ProcessFactory implements ProcessFactoryInterface
{
    /**
     * Create a new Process instance.
     *
     * @param string $commandLine
     *
     * @return Process
     */
    public function create(string $commandLine): Process
    {
        return new Process([$commandLine]);
    }
}
