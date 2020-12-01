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
        return method_exists(Process::class, 'fromShellCommandline')
            ? Process::fromShellCommandline($commandLine) // Symfony >= 4.2
            : new Process($commandLine); // Symfony < 4.2 (see https://github.com/composer/composer/blob/1.10.17/src/Composer/Util/ProcessExecutor.php#L68:L72)
    }
}
