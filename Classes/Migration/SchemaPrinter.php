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

use Cyberhouse\DoctrineORM\Database\CreateTablePrinter;
use Cyberhouse\DoctrineORM\Database\IdentifierQuotes;
use Doctrine\DBAL\Schema\Schema;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\StringUtility;

/**
 * Convert a schema to SQL statements
 *
 * @author Georg Großberger <georg.grossberger@cyberhouse.at>
 */
class SchemaPrinter implements SingletonInterface
{
    /**
     * @inject
     * @var \TYPO3\CMS\Extbase\Object\ObjectManager
     */
    protected $objectManager;

    /**
     * @var IdentifierQuotes
     */
    private $quotes;

    public function __construct()
    {
        $this->quotes = GeneralUtility::makeInstance(IdentifierQuotes::class);
    }

    public function toSQL(Schema $schema, string $extension): array
    {
        $platform = $this->objectManager
            ->get(ConnectionPool::class)
            ->getConnectionForTable($extension)
            ->getDatabasePlatform();

        $creates = [];
        $printer = $this->objectManager->get(CreateTablePrinter::class);

        foreach ($schema->toSql($platform) as $statement) {
            if (StringUtility::beginsWith($statement, 'CREATE TABLE ')) {
                $name = $this->quotes->remove(substr($statement, 13, stripos($statement, ' ', 13) - 13));

                if (isset($creates[$name])) {
                    throw new \UnexpectedValueException('Several create statements for table ' . $name . ' present');
                }

                $creates[$name] = $printer->getStatement($statement, false);
            }
        }

        return array_values($creates);
    }
}
