<?php
namespace Cyberhouse\DoctrineORM\Command;

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
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\StringUtility;

/**
 * Display all create commands in a TYPO3 ext_tables.php layout
 *
 * @author Georg Großberger <georg.grossberger@cyberhouse.at>
 */
class PrintCreateCommand extends DoctrineCommand
{
    protected function configure()
    {
        parent::configure();
        $this->setDescription('Print CREATE DDL statements in a TYPO3 ext_tables.php layout');
    }

    protected function executeCommand(OutputInterface $output): int
    {
        foreach ($this->extensions as $extension) {
            $em = $this->factory->get($extension);
            $metadatas = $this->getMetaData($em);
            $schemaTool = new SchemaTool($em);
            $sqls = $schemaTool->getCreateSchemaSql($metadatas);
            $printer = GeneralUtility::makeInstance(CreateTablePrinter::class);

            foreach ($sqls as $sql) {
                if (StringUtility::beginsWith($sql, 'CREATE TABLE')) {
                    $output->writeln('');
                    $output->writeln($printer->getStatement($sql));
                }
            }
        }
        return 0;
    }
}
