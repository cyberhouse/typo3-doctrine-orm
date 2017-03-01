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
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Schema\Parser\Parser;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Merge a given schema with the schema of an entity manager
 *
 * @author Georg Großberger <georg.grossberger@cyberhouse.at>
 */
class MigrationMerger
{
    /**
     * @var Schema
     */
    private $schema;

    /**
     * MigrationMerger constructor.
     *
     * @param array $source
     */
    public function __construct(array $source)
    {
        $parser = GeneralUtility::makeInstance(Parser::class, implode("\n", $source));
        $this->schema = new Schema($parser->parse());
    }

    public function mergeWith(EntityManager $em, string $extension)
    {
        $metadata = $em->getMetadataFactory()->getAllMetadata();
        $schemaTool = new SchemaTool($em);
        $additional = $schemaTool->getSchemaFromMetadata($metadata);

        $tables = [];
        $namespaces = [];

        /** @var Schema $schema */
        foreach ([$additional, $this->schema] as $schema) {
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

        $this->schema = new Schema(
            $tableObjects,
            $schema->getSequences(),
            $em->getConnection()->getSchemaManager()->createSchemaConfig(),
            array_unique($namespaces)
        );
    }

    public function getResult()
    {
        $cnx = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable('doctrine_orm')->getDatabasePlatform();
        $sql = [];
        $schemaTool = new SchemaTool('');
        $this->schema->toSql();

        return $sql;
    }
}
