<?php
/**
 * Copyright MediaCT. All rights reserved.
 * https://www.mediact.nl
 */

namespace MediaCT\TestingSuite\Composer;

use Iterator;

interface FileMappingReaderInterface extends Iterator
{
    /**
     * Get the current file mapping.
     *
     * @return FileMappingInterface
     */
    public function current(): FileMappingInterface;
}
