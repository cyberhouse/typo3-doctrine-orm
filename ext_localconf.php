<?php

/*
 * This file is (c) 2018 by Cyberhouse GmbH
 *
 * It is free software; you can redistribute it and/or
 * modify it under the terms of the GPLv3 license
 *
 * For the full copyright and license information see
 * <https://www.gnu.org/licenses/gpl-3.0.html>
 */

call_user_func(function () {
    $caches = & $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'];

    if (!isset($caches['doctrine_orm'])) {
        $caches['doctrine_orm'] = [];
    }

    $caches['doctrine_orm']['frontend'] = \Cyberhouse\DoctrineORM\Cache\DoctrineCapableFrontend::class;

    if (empty($caches['doctrine_orm']['backend'])) {
        $caches['doctrine_orm']['backend'] = \TYPO3\CMS\Core\Cache\Backend\SimpleFileBackend::class;

        if (!empty($caches['doctrine_orm']['options'])) {
            $caches['doctrine_orm']['options'] = [];
        }
    }

    if (empty($caches['doctrine_orm']['groups'])) {
        $caches['doctrine_orm']['groups'] = ['system'];
    }

    $defaultConfiguration = [
        'devMode'  => !\TYPO3\CMS\Core\Utility\GeneralUtility::getApplicationContext()->isProduction(),
        'proxyDir' => PATH_site . 'typo3temp/var/doctrine_proxies',
    ];

    if (empty($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['doctrine_orm'])) {
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['doctrine_orm'] = $defaultConfiguration;
    } else {
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['doctrine_orm'] = array_replace(
            $defaultConfiguration,
            $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['doctrine_orm']
        );
    }

    $signals = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\SignalSlot\Dispatcher::class);
    $signals->connect(
        \TYPO3\CMS\Install\Service\SqlExpectedSchemaService::class,
        'tablesDefinitionIsBeingBuilt',
        \Cyberhouse\DoctrineORM\Migration\DoctrineConnectionMigrator::class,
        'addEntitySQL'
    );
});
