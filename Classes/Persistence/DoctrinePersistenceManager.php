<?php
namespace Cyberhouse\DoctrineORM\Persistence;

/*
 * This file is (c) 2017 by Cyberhouse GmbH
 *
 * It is free software; you can redistribute it and/or
 * modify it under the terms of the GPLv3 license
 *
 * For the full copyright and license information see
 * <https://www.gnu.org/licenses/gpl-3.0.html>
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\Generic\Exception\NotImplementedException;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Property\TypeConverter\PersistentObjectConverter;

/**
 * Persistence manager for extbase that uses
 * a Doctrine ORM entity manager as backend
 *
 * @author Georg Großberger <georg.grossberger@cyberhouse.at>
 */
class DoctrinePersistenceManager implements PersistenceManagerInterface
{
    /**
     * @inject
     * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
     */
    protected $configuration;

    /**
     * @inject
     * @var \Cyberhouse\DoctrineORM\Utility\EntityManagerFactory
     */
    protected $factory;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var string
     */
    private $currentContext;

    public function __construct()
    {
        $this->setup();
    }

    protected function setup()
    {
        GeneralUtility::makeInstance(PersistentObjectConverter::class)->injectPersistenceManager($this);
    }

    protected function tearDown()
    {
        $generic = GeneralUtility::makeInstance(PersistenceManager::class);
        GeneralUtility::makeInstance(PersistentObjectConverter::class)->injectPersistenceManager($generic);
    }

    public function persistAll()
    {
        $em = $this->getEntityManager();

        if ($em->isOpen()) {
            $em->flush();
            $em->close();
        }

        $this->em = null;
        $this->factory->reset($this->currentContext);
        $this->tearDown();
    }

    public function clearState()
    {
        $this->getEntityManager()->clear();
        $this->factory->reset($this->currentContext);
        $this->em = null;
    }

    public function isNewObject($object)
    {
        return !($object instanceof AbstractEntity && $object->getUid() > 0);
    }

    public function getIdentifierByObject($object)
    {
        $id = null;

        if ($object instanceof AbstractEntity) {
            $id = $object->getUid();
        }

        return $id;
    }

    public function getObjectByIdentifier($identifier, $objectType = null, $useLazyLoading = false)
    {
        return $this->getEntityManager()->find($objectType, $identifier);
    }

    public function getObjectCountByQuery(QueryInterface $query)
    {
        return $query->count();
    }

    public function getObjectDataByQuery(QueryInterface $query)
    {
        return $query->execute(true);
    }

    public function add($object)
    {
        $this->getEntityManager()->persist($object);
    }

    public function remove($object)
    {
        $this->getEntityManager()->remove($object);
    }

    public function update($object)
    {
        $this->getEntityManager()->persist($object);
    }

    public function createQueryForType($type)
    {
        return GeneralUtility::makeInstance(DoctrineQuery::class, $this->getEntityManager(), $type);
    }

    public function registerRepositoryClassName($className)
    {
        throw new NotImplementedException('Not supported');
    }

    public function injectSettings(array $settings)
    {
        // noop, simply ignore any calls
    }

    public function convertObjectToIdentityArray($object)
    {
        throw new NotImplementedException('Not supported');
    }

    public function convertObjectsToIdentityArrays(array $array)
    {
        throw new NotImplementedException('Not supported');
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    protected function getEntityManager()
    {
        if (!$this->em) {
            $conf = $this->configuration->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);
            $this->currentContext = GeneralUtility::camelCaseToLowerCaseUnderscored($conf['extensionName']);
            $this->em = $this->factory->get($this->currentContext);
        }
        return $this->em;
    }
}
