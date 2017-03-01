<?php
namespace Cyberhouse\DoctrineORM\Utility;

/*
 * This file is (c) 2017 by Cyberhouse GmbH
 *
 * It is free software; you can redistribute it and/or
 * modify it under the terms of the GPLv3 license
 *
 * For the full copyright and license information see
 * <https://www.gnu.org/licenses/gpl-3.0.html>
 */

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Registry for extensions
 *
 * @author Georg Großberger <georg.grossberger@cyberhouse.at>
 */
class ExtensionRegistry implements SingletonInterface
{
    protected $registered = [];

    public function register(string $extKey, string ...$paths)
    {
        if (empty($paths)) {
            $paths[] = 'EXT:' . $extKey . '/Classes/Domain/Model';
        }
        $this->registered[$extKey] = $paths;
    }

    public function getExtensionPaths(string $extKey): array
    {
        $paths = [];

        if (isset($this->registered[$extKey])) {
            $paths = $this->registered[$extKey];
            $paths = array_map(function ($path) {
                if (strncmp($path, 'EXT:', 4) === 0) {
                    $path = GeneralUtility::getFileAbsFileName($path);
                }
                return $path;
            }, $paths);
            $paths = array_filter($paths, function ($path) {
                return !empty($path) && is_dir($path);
            });
        }

        if (empty($paths)) {
            throw new \UnexpectedValueException('No entity paths configured for ' . $extKey);
        }

        return $paths;
    }

    public function getRegisteredExtensions()
    {
        return array_keys($this->registered);
    }
}
