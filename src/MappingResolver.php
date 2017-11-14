<?php
/**
 * Copyright MediaCT. All rights reserved.
 * https://www.mediact.nl
 */

namespace Mediact\TestingSuite\Composer;

use Mediact\FileMapping\FileMappingReaderInterface;
use Mediact\FileMapping\UnixFileMappingReader;

class MappingResolver
{
    /** @var ProjectTypeResolver */
    private $typeResolver;

    /**
     * Constructor.
     *
     * @param ProjectTypeResolver $typeResolver
     */
    public function __construct(ProjectTypeResolver $typeResolver)
    {
        $this->typeResolver = $typeResolver;
    }

    /**
     * Resolve mapping files.
     *
     * @return FileMappingReaderInterface
     */
    public function resolve(): FileMappingReaderInterface
    {
        $files = [
            __DIR__ . '/../templates/mapping/files',
            sprintf(
                __DIR__ . '/../templates/mapping/project/%s',
                $this->typeResolver->resolve()
            )
        ];

        return new UnixFileMappingReader(
            __DIR__ . '/../templates/files',
            getcwd(),
            ...$files
        );
    }
}
