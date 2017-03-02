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
     * @var \TYPO3\CMS\Extbase\Object\ObjectManager
     */
    protected $objectManager;

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

    public function addEntitySQL(array $sqls)
    {
        $merger = $this->objectManager->get(MigrationMerger::class);
        $merger->initialize($sqls);

        foreach ($this->registry->getRegisteredExtensions() as $extension) {
            $em = $this->factory->get($extension);
            $merger->mergeWith($em, $extension);
        }

        return [$merger->getResult()];
    }
}
