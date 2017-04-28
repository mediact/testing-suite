<?php
/**
 * Copyright MediaCT. All rights reserved.
 * https://www.mediact.nl
 */

namespace MediaCT\TestingSuite\Composer;

class UnixFileMapping implements FileMappingInterface
{
    /** @var string */
    private $sourceDirectory;

    /** @var string */
    private $destinationDirectory;

    /** @var string */
    private $source;

    /** @var string */
    private $destination;

    /**
     * Constructor.
     *
     * @param string $sourceDirectory
     * @param string $destinationDirectory
     * @param string $mapping
     */
    public function __construct(
        string $sourceDirectory,
        string $destinationDirectory,
        string $mapping
    ) {
        $this->sourceDirectory      = $sourceDirectory;
        $this->destinationDirectory = $destinationDirectory;

        // Expand the source and destination.
        static $pattern    = '/({(.*),(.*)})/';
        $this->source      = preg_replace($pattern, '$2', $mapping);
        $this->destination = preg_replace($pattern, '$3', $mapping);
    }

    /**
     * Get the relative path to the source file.
     *
     * @return string
     */
    public function getRelativeSource(): string
    {
        return $this->source;
    }

    /**
     * Get the absolute path to the source file.
     *
     * @return string
     */
    public function getSource(): string
    {
        return $this->sourceDirectory
            . DIRECTORY_SEPARATOR
            . $this->source;
    }

    /**
     * Get the relative path to the destination file.
     *
     * @return string
     */
    public function getRelativeDestination(): string
    {
        return $this->destination;
    }

    /**
     * Get the absolute path to the destination file.
     *
     * @return string
     */
    public function getDestination(): string
    {
        return $this->destinationDirectory
            . DIRECTORY_SEPARATOR
            . $this->destination;
    }
}
