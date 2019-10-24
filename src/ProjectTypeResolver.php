<?php
/**
 * Copyright MediaCT. All rights reserved.
 * https://www.mediact.nl
 */

namespace Mediact\TestingSuite\Composer;

use Composer\Composer;

/**
 * Resolves the project type.
 */
class ProjectTypeResolver
{
    /**
     * The key from the composer configuration which overwrites the type.
     */
    const COMPOSER_CONFIG_TYPE_KEY = 'testing-suite-type';

    /** @var Composer */
    private $composer;

    /** @var array */
    private $mapping = [
        'magento2-module' => 'magento2',
        'magento-module'  => 'magento1'
    ];

    /**
     * Constructor.
     *
     * @param Composer   $composer
     * @param array|null $mapping
     */
    public function __construct(Composer $composer, array $mapping = null)
    {
        $this->composer = $composer;
        $this->mapping  = $mapping ?? $this->mapping;
    }

    /**
     * Get the type.
     *
     * @return string
     */
    public function resolve(): string
    {
        $config = $this->composer->getConfig();
        if ($config->has(static::COMPOSER_CONFIG_TYPE_KEY)) {
            return $config->get(static::COMPOSER_CONFIG_TYPE_KEY);
        }

        $packageType = $this->composer->getPackage()->getType();

        return array_key_exists($packageType, $this->mapping)
            ? $this->mapping[$packageType]
            : 'default';
    }
}
