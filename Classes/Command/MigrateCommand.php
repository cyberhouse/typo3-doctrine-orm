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

use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Migrate all Doctrine ORM entity manager schemas
 *
 * @author Georg Großberger <georg.grossberger@cyberhouse.at>
 */
class MigrateCommand extends DoctrineCommand
{
    /**
     * @var bool
     */
    private $dryRun = false;

    protected function configure()
    {
        parent::configure();

        $this->setDescription('Migrate Doctrine entites only, ignoring all DDL in ext_tables.sql');

        $this->addOption(
            'dry-run',
            'd',
            InputOption::VALUE_OPTIONAL,
            'Print SQL statements to be executed instead of running them',
            false
        );
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);
        $this->dryRun = (bool) $input->getOption('dry-run');
    }

    protected function executeCommand(OutputInterface $output): int
    {
        foreach ($this->extensions as $extension) {
            $output->write('Migrating ' . $extension . ' ... ');

            $em = $this->factory->get($extension);
            $metadatas = $em->getMetadataFactory()->getAllMetadata();
            $schemaTool = new SchemaTool($em);
            $sqls = $schemaTool->getUpdateSchemaSql($metadatas, true);

            if (count($sqls) > 0) {
                if ($this->dryRun) {
                    $output->write("<comment>Dry run, skipping</comment>\n");
                } else {
                    $em->getConnection()->executeUpdate('SET foreign_key_checks = 0');
                    $schemaTool->updateSchema($metadatas, true);
                    $em->getConnection()->executeUpdate('SET foreign_key_checks = 1');
                    $output->write("<info>Done</info>\n");
                }

                if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE || $this->dryRun) {
                    if ($this->dryRun) {
                        $prefix = 'Would execute ';
                    } else {
                        $prefix = 'Executed ';
                    }
                    $output->writeln($prefix . count($sqls) . ' statements:');

                    foreach ($sqls as $sql) {
                        $output->writeln($sql);
                    }
                }
            } else {
                $output->write("<info>Nothing to do</info>\n");
            }
        }
        return 0;
    }
}
