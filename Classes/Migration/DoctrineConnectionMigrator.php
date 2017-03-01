<?php
namespace Cyberhouse\DoctrineORM\Migration;

/*
 * This file is (c) 2017 by Cyberhouse GmbH
 *
 * It is free software; you can redistribute it and/or
 * modify it under the terms of the GPLv3 license
 *
 * For the full copyright and license information see
 * <https://www.gnu.org/licenses/gpl-3.0.html>
 */

use Cyberhouse\DoctrineORM\Utility\EntityManagerFactory;
use Cyberhouse\DoctrineORM\Utility\ExtensionRegistry;
use Doctrine\DBAL\Schema\Schema;
use TYPO3\CMS\Core\Database\Schema\ConnectionMigrator;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * Add Doctrine entity schema definitions to the database
 * migration of the default migrator
 *
 * @author Georg Großberger <georg.grossberger@cyberhouse.at>
 */
class DoctrineConnectionMigrator extends ConnectionMigrator
{
    public function checkConnectionForDoctrineMigrations(Schema $expected, string $connectionName): Schema
    {
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $registry = $objectManager->get(ExtensionRegistry::class);

        foreach ($registry->getRegisteredExtensions() as $extension) {
            if ($this->getConnectionNameForTable($extension) === $connectionName) {
                $factory = $objectManager->get(EntityManagerFactory::class);
                $em = $factory->get($extension);
                $merger = GeneralUtility::makeInstance(MigrationMerger::class, $em);
                $expected = $merger->mergeWith($expected);
            }
        }

        return $expected;
    }
    protected function buildExpectedSchemaDefinitions(string $connectionName): Schema
    {
        $expected = parent::buildExpectedSchemaDefinitions($connectionName);
        return $this->checkConnectionForDoctrineMigrations($expected, $connectionName);
    }
}
