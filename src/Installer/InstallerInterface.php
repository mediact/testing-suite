<?php
/**
 * Copyright MediaCT. All rights reserved.
 * https://www.mediact.nl
 */

namespace Mediact\TestingSuite\Composer\Installer;

interface InstallerInterface
{
    /**
     * Install.
     *
     * @return void
     */
    public function install();
}
