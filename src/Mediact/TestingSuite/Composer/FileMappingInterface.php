<?php
/**
 * Copyright MediaCT. All rights reserved.
 * https://www.mediact.nl
 */

namespace MediaCT\TestingSuite\Composer;

interface FileMappingInterface
{
    /**
     * Get the relative path to the source file.
     *
     * @return string
     */
    public function getRelativeSource(): string;

    /**
     * Get the absolute path to the source file.
     *
     * @return string
     */
    public function getSource(): string;

    /**
     * Get the relative path to the destination file.
     *
     * @return string
     */
    public function getRelativeDestination(): string;

    /**
     * Get the absolute path to the destination file.
     *
     * @return string
     */
    public function getDestination(): string;
}
