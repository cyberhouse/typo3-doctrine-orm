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

use Cyberhouse\DoctrineORM\Database\IdentifierQuotes;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\Table;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
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
     * @inject
     * @var \TYPO3\CMS\Extbase\Object\ObjectManager
     */
    protected $objectManager;

    /**
     * @var Schema
     */
    protected $schema;

    /**
     * @var string
     */
    protected $extension;

    /**
     * @var IdentifierQuotes
     */
    private $quotes;

    public function __construct()
    {
        $this->quotes = GeneralUtility::makeInstance(IdentifierQuotes::class);
    }

    /**
     * MigrationMerger constructor.
     *
     * @param array $source
     */
    public function initialize(array $source)
    {
        $tables = [];
        $reader = $this->objectManager->get(SqlReader::class);

        foreach ($source as $statement) {
            $creates = $reader->getCreateTableStatementArray($statement);

            foreach ($creates as $createStatement) {
                $parser = $this->objectManager->get(Parser::class, $createStatement);

                /** @var Table $table */
                foreach ($parser->parse() as $table) {
                    $name = $this->quotes->remove($table->getName());

                    if (isset($tables[$name])) {
                        $merger = $this->objectManager->get(TableMerger::class, $tables[$name]);
                        $table = $merger->mergeWith($table);
                    }

                    $tables[$name] = $table;
                }
            }
        }

        $this->schema = $this->objectManager->get(Schema::class, array_values($tables));
    }

    public function mergeWith(EntityManager $em, string $extension)
    {
        $metadata = $em->getMetadataFactory()->getAllMetadata();
        $schemaTool = $this->objectManager->get(SchemaTool::class, $em);
        $additional = $schemaTool->getSchemaFromMetadata($metadata);

        $tables = [];
        $namespaces = [];

        /** @var Schema $schema */
        foreach ([$additional, $this->schema] as $schema) {
            $namespaces = array_merge($namespaces, $schema->getNamespaces());

            foreach ($schema->getTables() as $table) {
                $name = $this->quotes->remove($table->getName());

                if (isset($tables[$name])) {
                    $merger = $this->objectManager->get(TableMerger::class, $tables[$name]);
                    $table = $merger->mergeWith($table);
                }

                $tables[$name] = $table;
            }
        }

        $config = $em->getConnection()->getSchemaManager()->createSchemaConfig();
        $config->setName($this->schema->getName());

        $this->extension = $extension;
        $this->schema = new Schema(
            array_values($tables),
            [],
            $config,
            array_unique($namespaces)
        );
    }

    public function getResult()
    {
        $printer = $this->objectManager->get(SchemaPrinter::class);
        $result = array_map(function ($entry) {
            return $entry . ';' . LF;
        }, $printer->toSQL($this->schema, $this->extension));
        return $result;
    }
}
