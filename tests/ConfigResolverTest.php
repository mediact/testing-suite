<?php
/**
 * Copyright MediaCT. All rights reserved.
 * https://www.mediact.nl
 */

namespace Mediact\TestingSuite\Composer\Tests;

use Mediact\TestingSuite\Composer\ProjectTypeResolver;
use PHPUnit\Framework\TestCase;
use Mediact\TestingSuite\Composer\ConfigResolver;

/**
 * @coversDefaultClass \Mediact\TestingSuite\Composer\ConfigResolver
 */
class ConfigResolverTest extends TestCase
{
    /**
     * @return void
     *
     * @covers ::__construct
     * @covers ::resolve
     */
    public function testResolve(): void
    {
        $typeResolver = $this->createMock(ProjectTypeResolver::class);

        $resolver = new ConfigResolver($typeResolver);

        $result = $resolver->resolve();

        $this->assertArrayHasKey('sort-packages', $result);
    }
}
