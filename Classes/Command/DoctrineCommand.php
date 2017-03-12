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

use Cyberhouse\DoctrineORM\Utility\EntityManagerFactory;
use Cyberhouse\DoctrineORM\Utility\ExtensionRegistry;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\StringUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * Base for EXT:doctrine_orm commands
 *
 * @author Georg Großberger <georg.grossberger@cyberhouse.at>
 */
abstract class DoctrineCommand extends Command
{
    /**
     * @var EntityManagerFactory
     */
    protected $factory;

    /**
     * @var array|[]string
     */
    protected $extensions = [];

    /**
     * @var bool
     */
    protected $dryRun = false;

    protected function configure()
    {
        $this->addOption(
            'extension',
            'e',
            InputOption::VALUE_OPTIONAL,
            'Limit migration to given extension'
        );

        $this->addOption(
            'dry-run',
            'd',
            InputOption::VALUE_OPTIONAL,
            'Print actions to be executed instead of running them',
            false
        );
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $output->setDecorated(true);

            $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
            $registry = $objectManager->get(ExtensionRegistry::class);
            $extensions = $registry->getRegisteredExtensions();

            if (empty($extensions)) {
                throw new \UnexpectedValueException('No extensions registered');
            }

            if (trim($input->getOption('extension')) !== '') {
                $limitTo = (string) $input->getOption('extension');

                if (!in_array($limitTo, $extensions)) {
                    throw new \InvalidArgumentException('No such extension registered');
                }

                $extensions = [$limitTo];
            }

            $this->extensions = $extensions;
            $this->factory = $objectManager->get(EntityManagerFactory::class);
            $this->dryRun = (bool) $input->getOption('dry-run');

            return $this->executeCommand($output);
        } catch (\Throwable $ex) {
            $output->writeln('<error>' . $ex->getMessage() . '</error>');
            return 1;
        }
    }

    protected function getMetaData(EntityManager $em)
    {
        $metadatas = $em->getMetadataFactory()->getAllMetadata();
        $metadatas = array_filter($metadatas, function (\Doctrine\ORM\Mapping\ClassMetadata $class) {
            $result = true;

            if ($class->name !== 'Cyberhouse\\DoctrineORM\\Domain\\Model\\AbstractDoctrineEntity') {
                if (StringUtility::beginsWith($class->name, 'Cyberhouse\\DoctrineORM\\')) {
                    $result = false;
                }
            }
            return $result;
        });

        return $metadatas;
    }

    abstract protected function executeCommand(OutputInterface $output): int;
}
