<?php
/**
 * Copyright MediaCT. All rights reserved.
 * https://www.mediact.nl
 */

namespace Mediact\TestingSuite\Composer\Tests\Factory;

use PHPUnit\Framework\TestCase;
use Mediact\TestingSuite\Composer\Factory\ProcessFactory;
use Symfony\Component\Process\Process;

/**
 * @coversDefaultClass \Mediact\TestingSuite\Composer\Factory\ProcessFactory
 */
class ProcessFactoryTest extends TestCase
{
    /**
     * @return void
     *
     * @covers ::create
     */
    public function testCreate()
    {
        $factory = new ProcessFactory();

        $this->assertInstanceOf(
            Process::class,
            $factory->create('foo')
        );
    }
}
