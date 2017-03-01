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

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\Table;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;

/**
 * Merge a given schema with the schema of an entity manager
 *
 * @author Georg Großberger <georg.grossberger@cyberhouse.at>
 */
class MigrationMerger
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * MigrationMerger constructor.
     *
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function mergeWith(Schema $schema)
    {
        $metadata = $this->em->getMetadataFactory()->getAllMetadata();
        $schemaTool = new SchemaTool($this->em);
        $additional = $schemaTool->getSchemaFromMetadata($metadata);

        $tables = [];
        $namespaces = [];

        /** @var Schema $schema */
        foreach ([$additional, $schema] as $schema) {
            $namespaces = array_merge($namespaces, $schema->getNamespaces());

            foreach ($schema->getTables() as $table) {
                $name = $table->getName();

                if (!isset($tables[$name])) {
                    $tables[$name] = [
                        'columns'       => [],
                        'indexes'       => [],
                        'fkConstraints' => [],
                        'options'       => $table->getOptions(),
                    ];
                }

                foreach ($table->getColumns() as $column) {
                    $tables[$name]['columns'][$column->getName()] = $column;
                }

                foreach ($table->getIndexes() as $index) {
                    $tables[$name]['indexes'][$index->getName()] = $index;
                }

                foreach ($table->getForeignKeys() as $fk) {
                    $tables[$name]['fkConstraints'][$fk->getName()] = $fk;
                }
            }
        }

        $tableObjects = [];

        foreach ($tables as $name => $data) {
            $tableObjects[] = new Table(
                $name,
                array_values($data['columns']),
                array_values($data['indexes']),
                array_values($data['fkConstraints']),
                0,
                $data['options']
            );
        }

        $expected = new Schema(
            $tableObjects,
            $schema->getSequences(),
            $this->em->getConnection()->getSchemaManager()->createSchemaConfig(),
            array_unique($namespaces)
        );

        return $expected;
    }
}
