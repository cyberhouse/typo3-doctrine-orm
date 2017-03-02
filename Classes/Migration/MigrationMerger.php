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
use TYPO3\CMS\Core\Database\Schema\Exception\StatementException;
use TYPO3\CMS\Core\Database\Schema\Parser\Parser;
use TYPO3\CMS\Core\Database\Schema\SqlReader;
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
     * @var array|[]string
     */
    private $result = [];

    /**
     * MigrationMerger constructor.
     *
     * @param array $source
     */
    public function __construct(array $source)
    {
        $tables = [];
        $reader = GeneralUtility::makeInstance(SqlReader::class);

        foreach ($source as $statement) {
            $creates = $reader->getCreateTableStatementArray($statement);

            foreach ($creates as $createStatement) {
                $parser = GeneralUtility::makeInstance(Parser::class, $createStatement);

                try {
                    foreach ($parser->parse() as $table) {
                        $name = $this->unquote($table->getName());

                        if (isset($tables[$name])) {
                            $table = $this->mergeTables($tables[$name], $table);
                        }

                        $tables[$name] = $table;
                    }
                } catch (StatementException $statementException) {
                    // Enrich the error message with the full invalid statement
                    throw new StatementException(
                        $statementException->getMessage() . ' in statement: ' . LF . $createStatement,
                        1476171315,
                        $statementException
                    );
                }
            }
        }

        $this->schema = new Schema(array_values($tables));
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
                $name = $this->unquote($table->getName());

                if (isset($tables[$name])) {
                    $table = $this->mergeTables($tables[$name], $table);
                }

                $tables[$name] = $table;
            }
        }

        $config = $em->getConnection()->getSchemaManager()->createSchemaConfig();
        $config->setName($this->schema->getName());

        $this->schema = new Schema(
            array_values($tables),
            [],
            $config,
            array_unique($namespaces)
        );

        $platform = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable($extension)
            ->getDatabasePlatform();
        $this->result = $this->schema->toSql($platform);
    }

    public function getResult()
    {
        $result = array_map(function ($entry) {
            return $entry . ';' . LF;
        }, $this->result);
        return $result;
    }

    protected function mergeTables(Table $a, Table $b): Table
    {
        $data = [
            'columns'       => [],
            'indexes'       => [],
            'fkConstraints' => [],
            'options'       => $a->getOptions(),
        ];

        foreach ([$a, $b] as $table) {
            foreach ($table->getColumns() as $column) {
                $data['columns'][$this->unquote($column->getName())] = $column;
            }

            foreach ($table->getIndexes() as $index) {
                $data['indexes'][$this->unquote($index->getName())] = $index;
            }

            foreach ($table->getForeignKeys() as $fk) {
                $data['fkConstraints'][$this->unquote($fk->getName())] = $fk;
            }
        }

        return new Table(
            $a->getName(),
            array_values($data['columns']),
            array_values($data['indexes']),
            array_values($data['fkConstraints']),
            0,
            $data['options']
        );
    }

    protected function unquote($identifier)
    {
        return trim(trim(trim($identifier), '`'));
    }
}
