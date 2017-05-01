<?php
/**
 * Copyright MediaCT. All rights reserved.
 * https://www.mediact.nl
 */

namespace MediaCT\TestingSuite\Composer;

use ArrayIterator;
use Iterator;
use SplFileObject;

class UnixFileMappingReader implements FileMappingReaderInterface
{
    /** @var array */
    private $mappingFilePaths;

    /** @var string */
    private $sourceDirectory;

    /** @var string */
    private $targetDirectory;

    /** @var Iterator|FileMappingInterface[] */
    private $mappings;

    /**
     * Constructor.
     *
     * @param string $mappingFilePaths
     * @param string $sourceDirectory
     * @param string $targetDirectory
     */
    public function __construct(
        string $mappingFilePaths,
        string $sourceDirectory,
        string $targetDirectory
    ) {
        $this->mappingFilePaths = $mappingFilePaths;
        $this->sourceDirectory  = $sourceDirectory;
        $this->targetDirectory  = $targetDirectory;
    }

    /**
     * Get the mappings.
     *
     * @return Iterator
     */
    private function getMappings(): Iterator
    {
        if ($this->mappings === null) {
            $filePaths = [];

            foreach ($this->mappingFilePaths as $mappingFilePath) {
                $newFilePaths = iterator_to_array(new SplFileObject($mappingFilePath, 'r'));
                $filePaths    = array_merge($filePaths, $newFilePaths);
            }

            $this->mappings = new ArrayIterator(
                array_map(
                    function (string $mapping) : FileMappingInterface {
                        return new UnixFileMapping(
                            $this->sourceDirectory,
                            $this->targetDirectory,
                            trim($mapping)
                        );
                    },
                    // Filter out empty lines.
                    array_filter(
                        $filePaths
                    )
                )
            );
        }

        return $this->mappings;
    }

    /**
     * Move forward to next element.
     *
     * @return void
     */
    public function next()
    {
        $this->getMappings()->next();
    }

    /**
     * Return the key of the current element.
     *
     * @return int
     */
    public function key(): int
    {
        return $this->getMappings()->key();
    }

    /**
     * Checks if current position is valid.
     *
     * @return bool
     */
    public function valid(): bool
    {
        return $this->getMappings()->valid();
    }

    /**
     * Rewind the Iterator to the first element.
     *
     * @return void
     */
    public function rewind()
    {
        $this->getMappings()->rewind();
    }

    /**
     * Get the current file mapping.
     *
     * @return FileMappingInterface
     */
    public function current(): FileMappingInterface
    {
        return $this->getMappings()->current();
    }
}
