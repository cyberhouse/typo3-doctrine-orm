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

use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Command to generate the proxy classes
 *
 * @author Georg Großberger <georg.grossberger@cyberhouse.at>
 */
class ProxiesCommand extends DoctrineCommand
{
    protected function configure()
    {
        parent::configure();
        $this->setDescription('Generate proxy classes of entities');
    }

    protected function executeCommand(OutputInterface $output): int
    {
        foreach ($this->extensions as $extension) {
            $output->write('Generating proxies of ' . $extension . ' ... ');

            $em = $this->factory->get($extension);
            $metadatas = $em->getMetadataFactory()->getAllMetadata();
            $destDir = $em->getConfiguration()->getProxyDir();

            if (!$this->dryRun) {
                GeneralUtility::mkdir_deep($destDir);
                $em->getProxyFactory()->generateProxyClasses($metadatas, $destDir);
                $output->write("<info>Done</info>\n");
            } else {
                $output->write("<info>Dry run, skipping</info>\n");
            }

            if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE || $this->dryRun) {
                if ($this->dryRun) {
                    $prefix = 'Would have generated ';
                } else {
                    $prefix = 'Generated ';
                }

                $output->writeln($prefix . count($metadatas) . ' proxies:');

                foreach ($metadatas as $metadata) {
                    $output->writeln($metadata->name);
                }
            }
        }
        return 0;
    }
}
