<?php

/*
 * This file is (c) 2017 by Cyberhouse GmbH
 *
 * It is free software; you can redistribute it and/or
 * modify it under the terms of the GPLv3 license
 *
 * For the full copyright and license information see
 * <https://www.gnu.org/licenses/gpl-3.0.html>
 */

$EM_CONF[$_EXTKEY] = [
    'title'              => 'TYPO3 Doctrine ORM',
    'description'        => 'Provides Extbase integration for Doctrine ORM for TYPO3 8',
    'category'           => 'misc',
    'shy'                => 0,
    'dependencies'       => '',
    'conflicts'          => '',
    'priority'           => '',
    'loadOrder'          => '',
    'module'             => '',
    'state'              => 'stable',
    'internal'           => 0,
    'uploadfolder'       => 0,
    'createDirs'         => '',
    'modify_tables'      => '',
    'clearCacheOnLoad'   => 1,
    'lockType'           => '',
    'author'             => 'Cyberhouse',
    'author_email'       => 'typo3@cyberhouse.at',
    'author_company'     => '',
    'CGLcompliance'      => '',
    'CGLcompliance_note' => '',
    'version'            => '1.0.3',
    'constraints'        => [
        'depends' => [
            'typo3'   => '8.6.0-8.7.99',
        ],
        'conflicts' => [],
        'suggests'  => [],
    ],
];
