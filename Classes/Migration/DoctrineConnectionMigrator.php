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
use Doctrine\ORM\EntityManager;
use TYPO3\CMS\Core\Cache\DatabaseSchemaService;
use TYPO3\CMS\Core\Database\Schema\ConnectionMigrator;
use TYPO3\CMS\Core\Database\Schema\SchemaMigrator;
use TYPO3\CMS\Core\Database\Schema\SqlReader;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * Add Doctrine entity schema definitions to the database
 * migration of the default migrator
 *
 * @author Georg Großberger <georg.grossberger@cyberhouse.at>
 */
class DoctrineConnectionMigrator
{
    /**
     * @inject
     * @var \Cyberhouse\DoctrineORM\Utility\ExtensionRegistry
     */
    protected $registry;

    /**
     * @inject
     * @var \Cyberhouse\DoctrineORM\Utility\EntityManagerFactory
     */
    protected $factory;

    public function injectEntitySQL(array $sqls)
    {
        $merger = GeneralUtility::makeInstance(MigrationMerger::class, $sqls);

        foreach ($this->registry->getRegisteredExtensions() as $extension) {
            $em = $this->factory->get($extension);
            $merger->mergeWith($em, $extension);
        }

        return $merger->getResult();
    }
}
