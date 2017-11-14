<?php
/**
 * Copyright MediaCT. All rights reserved.
 * https://www.mediact.nl
 */

namespace Mediact\TestingSuite\Composer\Tests;

use Mediact\FileMapping\FileMappingReaderInterface;
use Mediact\TestingSuite\Composer\ProjectTypeResolver;
use PHPUnit\Framework\TestCase;
use Mediact\TestingSuite\Composer\MappingResolver;

/**
 * @coversDefaultClass \Mediact\TestingSuite\Composer\MappingResolver
 */
class MappingResolverTest extends TestCase
{
    /**
     * @return void
     *
     * @covers ::__construct
     * @covers ::resolve
     */
    public function testResolve()
    {
        $typeResolver = $this->createMock(ProjectTypeResolver::class);
        $typeResolver
            ->expects(self::once())
            ->method('resolve')
            ->willReturn('foo');

        $mappingResolver = new MappingResolver($typeResolver);

        $this->assertInstanceOf(
            FileMappingReaderInterface::class,
            $mappingResolver->resolve()
        );
    }
}
