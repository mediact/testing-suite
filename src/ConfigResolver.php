<?php
/**
 * Copyright MediaCT. All rights reserved.
 * https://www.mediact.nl
 */

namespace Mediact\TestingSuite\Composer;

class ConfigResolver
{
    /** @var ProjectTypeResolver */
    private $typeResolver;

    /** @var string */
    private $template = __DIR__  . '/../templates/config/%s.json';

    /**
     * Constructor.
     *
     * @param ProjectTypeResolver $typeResolver
     * @param string              $template
     */
    public function __construct(
        ProjectTypeResolver $typeResolver,
        string $template = null
    ) {
        $this->typeResolver = $typeResolver;
        $this->template     = $template ?? $this->template;
    }

    /**
     * Resolve config.
     *
     * @return string[]
     */
    public function resolve(): array
    {
        $file = sprintf($this->template, $this->typeResolver->resolve());

        if (!file_exists($file)) {
            $file = sprintf($this->template, 'default');
        }

        return json_decode(file_get_contents($file), true);
    }
}
