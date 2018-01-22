<?php
namespace Cyberhouse\DoctrineORM\Migration;

/*
 * This file is (c) 2018 by Cyberhouse GmbH
 *
 * It is free software; you can redistribute it and/or
 * modify it under the terms of the GPLv3 license
 *
 * For the full copyright and license information see
 * <https://www.gnu.org/licenses/gpl-3.0.html>
 */

use Cyberhouse\DoctrineORM\Database\IdentifierQuotes;
use Doctrine\DBAL\Schema\Table;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Merge the definitions of two tables
 *
 * @author Georg Großberger <georg.grossberger@cyberhouse.at>
 */
class TableMerger
{
    /**
     * @var IdentifierQuotes
     */
    private $quotes;

    /**
     * @var Table
     */
    private $base;

    /**
     * TableMerger constructor.
     *
     * @param Table $base
     */
    public function __construct(Table $base)
    {
        $this->base = $base;
        $this->quotes = GeneralUtility::makeInstance(IdentifierQuotes::class);
    }

    public function mergeWith(Table $additional)
    {
        $data = [
            'columns'       => [],
            'indexes'       => [],
            'fkConstraints' => [],
            'options'       => $this->base->getOptions(),
        ];

        /** @var Table $table */
        foreach ([$this->base, $additional] as $table) {
            foreach ($table->getColumns() as $column) {
                if (!isset($data['columns'][$this->quotes->remove($column->getName())])) {
                    $data['columns'][$this->quotes->remove($column->getName())] = $column;
                }
            }

            foreach ($table->getIndexes() as $index) {
                if (!isset($data['indexes'][$this->quotes->remove($index->getName())])) {
                    $data['indexes'][$this->quotes->remove($index->getName())] = $index;
                }
            }

            foreach ($table->getForeignKeys() as $fk) {
                if (!isset($data['fkConstraints'][$this->quotes->remove($fk->getName())])) {
                    $data['fkConstraints'][$this->quotes->remove($fk->getName())] = $fk;
                }
            }
        }

        return new Table(
            $this->base->getName(),
            array_values($data['columns']),
            array_values($data['indexes']),
            array_values($data['fkConstraints']),
            0,
            $data['options']
        );
    }
}
