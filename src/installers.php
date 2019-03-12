<?php
/**
 * Copyright MediaCT. All rights reserved.
 * https://www.mediact.nl
 */

use Mediact\Composer\FileInstaller;
use Mediact\FileMapping\UnixFileMappingReader;
use Mediact\TestingSuite\Composer\Factory\ProcessFactory;
use Mediact\TestingSuite\Composer\Installer\ArchiveExcludeInstaller;
use Mediact\TestingSuite\Composer\Installer\ConfigInstaller;
use Mediact\TestingSuite\Composer\Installer\FilesInstaller;
use Mediact\TestingSuite\Composer\Installer\GrumPhpInstaller;
use Mediact\TestingSuite\Composer\Installer\PackagesInstaller;
use Mediact\TestingSuite\Composer\Installer\PipelinesInstaller;
use Mediact\TestingSuite\Composer\MappingResolver;
use Mediact\TestingSuite\Composer\ProjectTypeResolver;
use Mediact\TestingSuite\Composer\ConfigResolver;

/**
 * @var Composer\Composer       $composer
 * @var Composer\IO\IOInterface $io
 */

$typeResolver    = new ProjectTypeResolver($composer);
$mappingResolver = new MappingResolver($typeResolver);
$configResolver  = new ConfigResolver($typeResolver);
$fileInstaller   = new FileInstaller(
    new UnixFileMappingReader('', '')
);
$processFactory  = new ProcessFactory();

return [
    new FilesInstaller($mappingResolver, $fileInstaller, $io),
    new GrumPhpInstaller($io),
    new ArchiveExcludeInstaller($mappingResolver, $io),
    new PackagesInstaller($composer, $typeResolver, $io),
    new PipelinesInstaller($fileInstaller, $io, $processFactory, $typeResolver),
    new ConfigInstaller($configResolver, $io)
];
